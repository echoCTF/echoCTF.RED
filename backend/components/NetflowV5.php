<?php

namespace app\components;

/**
 * Minimal NetFlow v5 decoder for the datagrams OpenBSD pflow(4) sends
 * when configured with "pflowproto 5".
 *
 * pflow exports every pf state as two v5 records, one per direction,
 * when the state is removed from the state table.
 */
class NetflowV5
{
  const VERSION = 5;
  const HEADER_LEN = 24;
  const RECORD_LEN = 48;
  const MAX_RECORDS = 30;

  /**
   * Decode a single datagram.
   *
   * @param string $data raw UDP payload
   * @return array|false list of records, each with keys
   *   src, dst (unsigned 32 bit int), packets, bytes, srcport, dstport, proto,
   *   input, output (interface indexes of the pf state)
   *   or false when the payload is not a valid v5 datagram
   */
  public static function decode(string $data)
  {
    $len = strlen($data);
    if ($len < self::HEADER_LEN)
      return false;

    $hdr = unpack('nversion/ncount', $data);
    if ($hdr['version'] !== self::VERSION || $hdr['count'] < 1 || $hdr['count'] > self::MAX_RECORDS)
      return false;

    if ($len < self::HEADER_LEN + $hdr['count'] * self::RECORD_LEN)
      return false;

    $records = [];
    for ($i = 0; $i < $hdr['count']; $i++) {
      $r = unpack(
        'Nsrc/Ndst/Nnexthop/ninput/noutput/Npackets/Nbytes/Nfirst/Nlast/nsrcport/ndstport/Cpad1/Ctcpflags/Cproto/Ctos',
        $data,
        self::HEADER_LEN + $i * self::RECORD_LEN
      );
      $records[] = [
        'src' => $r['src'],
        'dst' => $r['dst'],
        'packets' => $r['packets'],
        'bytes' => $r['bytes'],
        'srcport' => $r['srcport'],
        'dstport' => $r['dstport'],
        'proto' => $r['proto'],
        'input' => $r['input'],
        'output' => $r['output'],
      ];
    }
    return $records;
  }
}
