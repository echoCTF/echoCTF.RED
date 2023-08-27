#!/usr/bin/perl
#
# www.packetmischief.ca
#


my $num_talkers = 10;


my %talkers;
while (<>) {
        # vlan123 tcp 192.168.130.10:10120 -> 192.168.1.7:1025       ESTABLISHED:ESTABLISHED
        # vlan123 ospf 224.0.0.5 <- 192.168.252.34       NO_TRAFFIC:SINGLE
        m/^\w+\s+\w+\s+([\d\.]+)(:\d+)*\s+([\-\<\>]+)\s+([\d\.]+)/;
        my $direction = $3;
        my $sip = $1;
        my $dip = $4;
#        exit;
        if ($direction eq "<-") {

                $sip = $4;
                $dip = $1;
        }

#        print "$sip->$dip\n";

        if (defined $talkers{$sip})
        {
                $talkers{$sip}++;
        } else {
                $talkers{$sip} = 1;
        }
}

my @top_talkers = sort { $talkers{$b} <=> $talkers{$a} } keys %talkers;
my $i;
if($num_talkers > @top_talkers) {
        $num_talkers=@top_talkers;
}

for ($i = 0; $i < $num_talkers; $i++) {
        print $top_talkers[$i], " (", $talkers{$top_talkers[$i]}, ")\n";
}
