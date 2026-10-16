#!/usr/bin/perl
use Net::Pcap;
use NetPacket::Ethernet;
use NetPacket::IP;
use NetPacket::TCP;
use NetPacket::UDP;
use NetPacket::ICMP;
use Socket;
use DBI;
use strict;
$|++;

# Parent only watches the client socket (fd 0) and kills the capture child on
# disconnect; the child does everything else, including waiting for tun0.
# If the child exits, the parent exits too.
$SIG{CHLD} = sub { exit 0 };
my $kid = fork();
die "fork: $!" unless defined $kid;
if ($kid) {
    $SIG{PIPE} = 'IGNORE';
    my $fin = 0;
    while (1) {
        if (!$fin) {
            my $r = sysread(STDIN, my $buf, 4096);
            next if !defined $r && $!{EINTR};
            last if !defined $r;    # connection reset
            $fin = 1 unless $r;     # FIN: full close OR half close (client done sending, still reading)
        }
        else {
            # Only a write can tell the two apart: a closed peer answers with RST, so the
            # second write fails. A half-closed client keeps receiving (and gets a blank line).
            sleep 2;
            my $w = syswrite(STDOUT, "\n");
            last if !defined $w && !$!{EINTR};
        }
    }
    kill 'KILL', $kid;
    waitpid($kid, 0);
    exit 0;
}
$SIG{CHLD} = 'DEFAULT';

my $err;
#my $filter_str='( tcp or udp or icmp ) and ( dst net 10.0.0.0/16 )';
# Local IPv4 address of tun0, looked up on every start (inetd runs one process per
# connection). Waits, polling once a second, until tun0 has an inet line (openvpn
# may not be up yet).
my $tun_ip;
until (defined $tun_ip) {
    ($tun_ip) = `/sbin/ifconfig tun0 2>/dev/null` =~ /^\s*inet (\d+\.\d+\.\d+\.\d+)\s/m;
    sleep 1 unless defined $tun_ip;
}
my $filter_str="( tcp or udp or icmp ) and ( src net 10.10.0.0/16 and dst net 10.0.0.0/16 ) and not ( src host $tun_ip ) and not ( dst net 10.0.0.0/24 )";

# Per-service colour and name, keyed by destination port.
# Row: [ name, colour, ports... ]
# The colour is used by both outputs; the name is used in the gource path (22.ssh).
# Ports not listed keep the plain tcp/udp colour and show just the port number.
my %SERVICES;
for my $row (
    #  name             colour    ports
    [ 'ssh',           '2ECC71', 22 ],
    [ 'telnet',        'E74C3C', 23 ],
    [ 'ftp',           'FFA500', 20, 21 ],
    [ 'tftp',          'FFA500', 69 ],
    [ 'rsync',         'FFA500', 873 ],
    [ 'nfs',           'FFA500', 2049 ],
    [ 'smtp',          'F1C40F', 25, 465, 587 ],
    [ 'pop3',          'F1C40F', 110, 995 ],
    [ 'imap',          'F1C40F', 143, 993 ],
    [ 'dns',           '1ABC9C', 53 ],
    [ 'http',          '00BFFF', 80, 8000, 8080, 8888 ],
    [ 'https',         'FFFFFF', 443, 8443 ],
    [ 'smb',           'FF69B4', 139, 445 ],
    [ 'rdp',           'FF69B4', 3389 ],
    [ 'vnc',           'FF69B4', 5900, 5901 ],
    [ 'winrm',         'FF69B4', 5985, 5986 ],
    [ 'ldap',          'D7BDE2', 389, 636 ],
    [ 'kerberos',      'D7BDE2', 88 ],
    [ 'mysql',         'E056FD', 3306 ],
    [ 'postgres',      'E056FD', 5432 ],
    [ 'mssql',         'E056FD', 1433 ],
    [ 'oracle',        'E056FD', 1521 ],
    [ 'mongodb',       'E056FD', 27017 ],
    [ 'redis',         'E056FD', 6379 ],
    [ 'memcached',     'E056FD', 11211 ],
    [ 'elasticsearch', 'E056FD', 9200 ],
    [ 'ntp',           'AAB7B8', 123 ],
    [ 'snmp',          'AAB7B8', 161, 162 ],
    [ 'syslog',        'AAB7B8', 514 ],
    [ 'dhcp',          'AAB7B8', 67, 68 ],
    [ 'openvpn',       'AAB7B8', 1194 ],
    [ 'wireguard',     'AAB7B8', 51820 ],
    [ 'ipsec',         'AAB7B8', 500, 4500 ],
) {
    my ($name, $hex, @ports) = @$row;
    $SERVICES{$_} = [ $name, "#$hex" ] for @ports;
}

# ICMP type names for the gource path.
my %ICMP_TYPES = (
    0 => 'echo-reply',        3 => 'dest-unreachable', 4  => 'source-quench',
    5 => 'redirect',          8 => 'echo-request',     9  => 'router-advert',
    10 => 'router-solicit',   11 => 'time-exceeded',   12 => 'param-problem',
    13 => 'timestamp',        14 => 'timestamp-reply', 17 => 'mask-request',
    18 => 'mask-reply',
);

#   Use network device passed in program arguments or if no
#   argument is passed, determine an appropriate network
#   device for packet sniffing using the
#   Net::Pcap::lookupdev method

my $dev = $ARGV[0];
my $out=$ARGV[1];
unless (defined $dev) {
    $dev = Net::Pcap::lookupdev(\$err);
    if (defined $err) {
        die 'Unable to determine network device for monitoring - ', $err;
    }
}


my ($address, $netmask);
my $object;
# pcap_open_live($dev, $snaplen, $promisc, $to_ms, \$err)
# Returns a packet capture descriptor for looking at packets on the network.
# The $dev parameter specifies which network interface to capture packets from.
# The $snaplen and $promisc parameters specify the maximum number of bytes to
# capture from each packet, and whether to put the interface into promiscuous
# mode, respectively. The $to_ms parameter specifies a read timeout in
# milliseconds. The packet descriptor will be undefined if an error occurs, and
# the $err parameter will be set with an appropriate error message.
$object = Net::Pcap::pcap_open_live($dev, 1024, 0, 1, \$err);

unless (defined $object) {
    die 'Unable to create packet capture on device ', $dev, ' - ', $err;
}


my $filter;
Net::Pcap::compile(
    $object,
    \$filter,
    $filter_str,
    0,
    0
) && die 'Unable to compile packet capture filter';
Net::Pcap::setfilter($object, $filter) && die 'Unable to set packet capture filter';

Net::Pcap::loop($object, -1, \&syn_packets, '') ||
    die 'Unable to perform packet capture';

Net::Pcap::close($object);

sub gource {
  my ($srcip, $mtype, $dstip, $dstport, $prt, $color, $leaf) = @_;
  my $TIMESTAMP=time();
  # tree: CTF -> <dst ip> -> leaf, where leaf is <port>.<service>, <port>.<proto> or <icmp type>.icmp
  print "$TIMESTAMP|$srcip|$mtype|CTF/$dstip/$leaf|$color\n";
}
sub logstalgia {
  my ($srcip, $mtype, $dstip, $dstport, $prt, $color) = @_;
  my $TIMESTAMP=time();
  if ($dstport!="")
  {
    $dstport=".$dstport";
  }
  print "$TIMESTAMP|$srcip|/$dstip$dstport.$prt|200|1024|1|$color\n";

}

sub syn_packets {
    my ($user_data, $header, $packet) = @_;

    #   Strip ethernet encapsulation of captured packet
	  my $eth = NetPacket::Ethernet->decode($packet);
    my $ether_data = NetPacket::Ethernet::strip($packet);
    #   Decode contents of TCP/IP packet contained within
    #   captured ethernet packet
    my $ip 		= NetPacket::IP->decode($ether_data);
  	my $srcport 	= 0;
  	my $dstport 	= "";
  	my $size = $ip->{'len'};
  	my $prt="";
    my $color="";
    my $mtype="";

	  if ($ip->{'proto'}==6) {
      my $decoded_packet = NetPacket::TCP->decode($ip->{'data'});
  		$prt 	= "tcp";
      $color="#8E44AD";
      $mtype="A";
  		$srcport=$decoded_packet->{'src_port'};
  		$dstport=$decoded_packet->{'dest_port'};
    }
    elsif($ip->{'proto'}==1) {
        $color="#2E86C1";
		    $prt 	= "icmp";
        $mtype="A";
        $dstport="";
    }
    elsif($ip->{'proto'}==17) {
    	my $decoded_packet = NetPacket::UDP->decode($ip->{'data'});
    	$prt 	= "udp";
      $color="#FF5733";
      $mtype="M";
		  $srcport=$decoded_packet->{'src_port'};
		  $dstport=$decoded_packet->{'dest_port'};
    }

    #   Colour and service name by destination port (ICMP has none); unknown ports keep
    #   the protocol colour and are named <port>.<proto>.
    my $leaf = "$dstport.$prt";
    if ($dstport ne "" && exists $SERVICES{$dstport}) {
        $leaf  = "$dstport.$SERVICES{$dstport}[0]";
        $color = $SERVICES{$dstport}[1];
    }
    elsif ($prt eq "icmp") {
        my $icmp  = NetPacket::ICMP->decode($ip->{'data'});
        my $itype = $icmp->{'type'};
        $leaf = (exists $ICMP_TYPES{$itype} ? $ICMP_TYPES{$itype} : "type$itype") . ".icmp";
    }

	my $srchw 	= $eth->{src_mac};
	my $dsthw 	= $eth->{dest_mac};
	my $srcip 	= $ip->{'src_ip'};
	my $dstip 	= $ip->{'dest_ip'};
	$srchw=~ s/..\K\B/:/g;
	$dsthw=~ s/..\K\B/:/g;
  my $TIMESTAMP=time();

  if ( $out eq "g" ) {
    gource $srcip,$mtype,$dstip,$dstport,$prt,$color,$leaf;
  }
  else {
    logstalgia($srcip,$mtype,$dstip,$dstport,$prt,$color);
  }
}
