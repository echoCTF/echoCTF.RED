<?php

use yii\db\Migration;

/**
 * Adds repeating "doodle" layout overrides for birthdays and special days.
 *
 * Each doodle swaps the target versus complete/incomplete icons, the same way
 * the "echoCTF Birthday" and "Valentines Day" overrides do.
 *
 * NOTE: For repeating overrides, registerLayoutOverrides() rebuilds the dates
 * with DATE_FORMAT(...,'%Y-%m-%d'), so the time of day is ignored and
 * valid_until is treated as midnight. A single day doodle therefore runs from
 * the day itself until the start of the next day. Ranges must not cross the
 * new year (Dec -> Jan) since the month/day comparison would never match.
 */
class m261104_120000_add_doodle_layout_overrides extends Migration
{
  const SOLID = ['"Font Awesome 6 Free"', 900];
  const BRANDS = ['"Font Awesome 6 Brands"', 400];
  const TEXT = ['Georgia, serif', 700];

  /**
   * Each event: name, from (m-d), until (m-d, exclusive), complete icon,
   * incomplete icon and optional color. Icons are [codepoint, font] pairs.
   */
  public function events()
  {
    $s = self::SOLID;
    $b = self::BRANDS;
    return [
      // January
      ['New Years Day', '01-01', '01-02', ['\f79f', $s], ['\f252', $s], '#f1c40f'],
      ['Isaac Asimov Birthday', '01-02', '01-03', ['\f544', $s], ['\f02d', $s], null],
      ['Isaac Newton Birthday', '01-04', '01-05', ['\f5d1', $s], ['\f5cd', $s], '#c0392b'],
      ['Data Privacy Day', '01-28', '01-29', ['\f505', $s], ['\f21b', $s], null],
      // March
      ['Alexander Graham Bell Birthday', '03-03', '03-04', ['\f095', $s], ['\f3dd', $s], null],
      ['Global Engineering Day', '03-04', '03-05', ['\f085', $s], ['\f7d9', $s], null],
      ['Womens Day', '03-08', '03-09', ['\f221', $s], ['\f182', $s], '#8e44ad'],
      ['Pi Day', '03-14', '03-15', ['\03c0', self::TEXT], ['\f200', $s], null],
      ['Jean Sammet Birthday', '03-23', '03-24', ['\f698', $s], ['\f1c9', $s], null],
      ['Earth Hour', '03-26', '03-27', ['\f7a2', $s], ['\f011', $s], '#27ae60'],
      ['World Backup Day', '03-31', '04-01', ['\f0c7', $s], ['\f0ee', $s], null],
      // April
      ['CSS Naked Day', '04-05', '04-06', ['\f259', $s], ['\f13b', $b], null],
      ['Morse Code Day', '04-27', '04-28', ['\f519', $s], ['\f141', $s], null],
      ['World Wide Web Day', '04-30', '05-01', ['\f0ac', $s], ['\f0c1', $s], null],
      // May
      ['World Password Day', '05-07', '05-08', ['\f084', $s], ['\f023', $s], null],
      ['International Museum Day', '05-18', '05-19', ['\f19c', $s], ['\f145', $s], null],
      ['Ethernet Day', '05-22', '05-23', ['\f796', $s], ['\f6ff', $s], null],
      ['Geek Pride Day', '05-25', '05-26', ['\f530', $s], ['\f669', $s], null],
      // June
      ['World Science Festival', '06-01', '06-06', ['\f0c3', $s], ['\f610', $s], null],
      ['Alan Turing Birthday', '06-23', '06-24', ['\f5dc', $s], ['\f023', $s], null],
      // July
      ['Happy Ripping Day', '07-14', '07-15', ['\f51f', $s], ['\f001', $s], null],
      ['Gary Gygax Birthday', '07-27', '07-28', ['\f6cf', $s], ['\f6d5', $s], '#c0392b'],
      ['Sysadmin Appreciation Day', '07-31', '08-01', ['\f233', $s], ['\f120', $s], null],
      // August
      ['Frances Allen Birthday', '08-04', '08-05', ['\f625', $s], ['\f5fd', $s], null],
      ['IBM PC Day', '08-12', '08-13', ['\f390', $s], ['\f11c', $s], null],
      // September
      ['Programmer Day', '09-12', '09-13', ['\f121', $s], ['\f120', $s], '#2ecc71'],
      ['Software Freedom Day', '09-19', '09-20', ['\f17c', $b], ['\f4ba', $s], null],
      // October
      ['Virus Appreciation Day', '10-03', '10-04', ['\e075', $s], ['\e074', $s], '#27ae60'],
      ['Neil deGrasse Tyson Birthday', '10-05', '10-06', ['\f4fb', $s], ['\f005', $s], null],
      ['Ada Lovelace Day', '10-11', '10-12', ['\f091', $s], ['\f221', $s], '#8e44ad'],
      ['International Internet Day', '10-29', '10-30', ['\f1eb', $s], ['\f7c0', $s], null],
      // November
      ['Barbara Liskov Birthday', '11-07', '11-08', ['\f0ec', $s], ['\f1b3', $s], null],
      ['Computer Security Day', '11-30', '12-01', ['\f3ed', $s], ['\f714', $s], null],
      // December
      ['Kill a Bug Day', '12-02', '12-03', ['\e490', $s], ['\f188', $s], null],
      ['Grace Hopper Birthday', '12-09', '12-10', ['\f13d', $s], ['\f188', $s], null],
      ['Ada Lovelace Birthday', '12-10', '12-11', ['\f085', $s], ['\f70e', $s], null],
      ['Sister Mary Kenneth Keller Birthday', '12-17', '12-18', ['\f19d', $s], ['\f02d', $s], null],
      ['Radia Perlman Birthday', '12-18', '12-19', ['\f0e8', $s], ['\f542', $s], null],
      ['Christmas', '12-25', '12-26', ['\f06b', $s], ['\f786', $s], '#c0392b'],
      ['National Download Day', '12-28', '12-29', ['\f0ed', $s], ['\f110', $s], null],
    ];
  }

  /**
   * Tooltip text shown when hovering the versus icon, keyed by event name.
   */
  public function tips()
  {
    return [
      'New Years Day' => 'Happy New Year from echoCTF! New year, new targets.',
      'Isaac Asimov Birthday' => 'Isaac Asimov, born 2 January 1920. Author of I, Robot and the Three Laws of Robotics.',
      'Isaac Newton Birthday' => 'Sir Isaac Newton, born 4 January 1643. Laws of motion and universal gravitation.',
      'Data Privacy Day' => 'Data Privacy Day. Marks the signing of Convention 108, the first international data protection treaty, in 1981.',
      'Alexander Graham Bell Birthday' => 'Alexander Graham Bell, born 3 March 1847. Patented the telephone in 1876.',
      'Global Engineering Day' => 'Global Engineering Day. A day to celebrate the engineers who build our world.',
      'Womens Day' => 'International Women\'s Day. Celebrating the women who shaped computer science.',
      'Pi Day' => 'Pi Day: 3.14. Also Albert Einstein\'s birthday, born 14 March 1879.',
      'Jean Sammet Birthday' => 'Jean Sammet, born 23 March 1928. Created FORMAC and co-designed COBOL.',
      'Earth Hour' => 'Earth Hour. Switch off your non-essential lights for an hour (your attack box can stay on).',
      'World Backup Day' => 'World Backup Day. Back up your files today, before April Fools\' Day does it for you.',
      'CSS Naked Day' => 'CSS Naked Day, celebrated since 2006. Also First Contact Day, 5 April 2063. Live long and prosper.',
      'Morse Code Day' => 'Morse Code Day. Samuel Morse was born on 27 April 1791. -.-. - ..-.',
      'World Wide Web Day' => 'On 30 April 1993 CERN put the World Wide Web into the public domain.',
      'World Password Day' => 'World Password Day. Use a password manager and turn on 2FA.',
      'International Museum Day' => 'International Museum Day, held every year since 1977 by the International Council of Museums.',
      'Ethernet Day' => 'On 22 May 1973 Bob Metcalfe wrote the memo describing Ethernet.',
      'Geek Pride Day' => 'Geek Pride Day. Star Wars premiered on 25 May 1977. It is also Towel Day.',
      'World Science Festival' => 'World Science Festival. Go break something, scientifically.',
      'Alan Turing Birthday' => 'Alan Turing, born 23 June 1912. Father of theoretical computer science and Enigma codebreaker.',
      'Happy Ripping Day' => 'Happy Ripping Day. On 14 July 1995 Fraunhofer picked .mp3 as the file extension.',
      'Gary Gygax Birthday' => 'Gary Gygax, born 27 July 1938. Co-creator of Dungeons & Dragons. Roll for initiative.',
      'Sysadmin Appreciation Day' => 'Sysadmin Appreciation Day. Thank the people who keep the servers (and these targets) running.',
      'Frances Allen Birthday' => 'Frances Allen, born 4 August 1932. Pioneer of compiler optimization and the first woman to win the Turing Award.',
      'IBM PC Day' => 'On 12 August 1981 IBM introduced the Model 5150 Personal Computer.',
      'Programmer Day' => 'Programmer Day, the 256th day of the year. Happy 0x100!',
      'Software Freedom Day' => 'Software Freedom Day. Celebrating free and open source software.',
      'Virus Appreciation Day' => 'Virus Appreciation Day and Techies Day. Thank your local techie, not your local virus.',
      'Neil deGrasse Tyson Birthday' => 'Neil deGrasse Tyson, born 5 October 1958. Astrophysicist and science communicator.',
      'Ada Lovelace Day' => 'Ada Lovelace Day. Celebrating the achievements of women in science and technology.',
      'International Internet Day' => 'International Internet Day. On 29 October 1969 the first ARPANET message was sent.',
      'Barbara Liskov Birthday' => 'Barbara Liskov, born 7 November 1939. Creator of CLU, the Liskov substitution principle, and 2008 Turing Award winner.',
      'Computer Security Day' => 'Computer Security Day, observed since 1988. Patch, update, repeat.',
      'Kill a Bug Day' => 'Kill a Bug Day. Squash one today.',
      'Grace Hopper Birthday' => 'Grace Hopper, born 9 December 1906. Built the first compiler, helped create COBOL and made "bug" famous.',
      'Ada Lovelace Birthday' => 'Ada Lovelace, born 10 December 1815. Published the first algorithm for Babbage\'s Analytical Engine.',
      'Sister Mary Kenneth Keller Birthday' => 'Sister Mary Kenneth Keller, born 17 December 1913. One of the first US women to earn a PhD in computer science, and worked on BASIC.',
      'Radia Perlman Birthday' => 'Radia Perlman, born 18 December 1951. Invented the Spanning Tree Protocol.',
      'Christmas' => 'Merry Christmas from echoCTF!',
      'National Download Day' => 'National Download Day. Grab something good (and check the hash).',
    ];
  }

  private function tooltipJs($tip)
  {
    return '$(".vscomplete,.vsincomplete").attr({"data-toggle":"tooltip","title":' . json_encode($tip, JSON_UNESCAPED_UNICODE) . '}).css("cursor","help");';
  }

  private function rule($class, $icon, $color)
  {
    list($content, $font) = $icon;
    $css = '.' . $class . '::before { ';
    if ($color !== null)
      $css .= 'color: ' . $color . '; ';
    $css .= 'font-family: ' . $font[0] . '; font-weight: ' . $font[1] . '; content: "' . $content . '";  }';
    return $css;
  }

  public function safeUp()
  {
    foreach ($this->events() as $e) {
      list($name, $from, $until, $complete, $incomplete, $color) = $e;
      $exists = (new \yii\db\Query())->from('layout_override')->where(['name' => $name])->exists();
      if ($exists)
        continue;
      $this->insert('layout_override', [
        'name' => $name,
        'route' => '',
        'player_id' => null,
        'guest' => 0,
        'repeating' => 1,
        'css' => $this->rule('vscomplete', $complete, $color) . ' ' . $this->rule('vsincomplete', $incomplete, $color),
        'js' => '',
        'valid_from' => '2026-' . $from . ' 00:00:00',
        'valid_until' => '2026-' . $until . ' 00:00:00',
      ]);
    }
  }

  public function safeDown()
  {
    $names = array_map(function ($e) {
      return $e[0];
    }, $this->events());
    $this->delete('layout_override', ['name' => $names]);
  }
}
