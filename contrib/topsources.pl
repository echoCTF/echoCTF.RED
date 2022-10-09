#!/usr/bin/perl
#
# www.packetmischief.ca
#
my $num_talkers = 10;
my %talkers;
while (<>) {
        m/^\w+\s+\w+\s+([\d\.]+)(:\d+)*\s+[\-\<\>]+\s+([\d\.]+)/;
        my $direction = $4;
        my $sip = $3;
        if ($direction eq "<-") {
                $sip = $5;
        }
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
      if($talkers{$top_talkers[$i]} > 20000)
      {
              print $top_talkers[$i], "\n";
      }
}
