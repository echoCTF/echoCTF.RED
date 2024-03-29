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
my $err;
#my $filter_str='( tcp or udp or icmp ) and ( dst net 10.0.0.0/16 )';
my $filter_str='( tcp or udp or icmp ) and ( src net 10.10.0.0/24 and dst net 10.0.160.0/24 ) and not ( src host 10.10.0.1 )';
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
my $dsn = "DBI:mysql:database=echoCTF;host=172.24.0.253;port=3306";
my $username = "vpnuser";
my $password = 'vpnuserpass';

# connect to MySQL database
my %attr = ( PrintError=>0,  # turn off error reporting via warn()
             RaiseError=>1   # report error via die()
           );
my $dbh = DBI->connect($dsn,$username,$password,\%attr);
my $sql = "INSERT INTO findingsd ( srcip, dstip,dstport, proto) VALUES (INET_ATON(?),INET_ATON(?),?,?)";
my $sth = $dbh->prepare($sql);

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

sub logPacket {
  my ($srcip, $mtype, $dstip, $dstport, $prt, $color) = @_;
  my $TIMESTAMP=time();
  print "$srcip => $dstip:$dstport/$prt\n";
  $sth->execute($srcip,$dstip,$dstport,$prt);
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
  	my $dstport 	= 0;
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
        $dstport=0;
    }
    elsif($ip->{'proto'}==17) {
    	my $decoded_packet = NetPacket::UDP->decode($ip->{'data'});
    	$prt 	= "udp";
      $color="#FF5733";
      $mtype="M";
		  $srcport=$decoded_packet->{'src_port'};
		  $dstport=$decoded_packet->{'dest_port'};
    }

	my $srchw 	= $eth->{src_mac};
	my $dsthw 	= $eth->{dest_mac};
	my $srcip 	= $ip->{'src_ip'};
	my $dstip 	= $ip->{'dest_ip'};
	$srchw=~ s/..\K\B/:/g;
	$dsthw=~ s/..\K\B/:/g;
  my $TIMESTAMP=time();
  logPacket $srcip,$mtype,$dstip,$dstport,$prt,$color;
}
