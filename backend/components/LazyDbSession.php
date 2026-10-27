<?php

namespace app\components;

use yii\db\Query;
use yii\web\DbSession;

/**
 * DbSession that skips the per-request write (INSERT ... ON DUPLICATE KEY UPDATE)
 * when the session data is unchanged and the expiry was refreshed recently.
 *
 * The row is still written when the data changes (login, logout, flash messages,
 * etc.) or when less than (timeout - touchInterval) seconds remain before expiry.
 */
class LazyDbSession extends DbSession
{
  /**
   * @var int seconds between expiry refreshes for an unchanged session
   */
  public $touchInterval = 300;

  private $_readId;
  private $_readData;
  private $_readExpire;

  public function readSession($id)
  {
    $this->_readId = $this->_readData = $this->_readExpire = null;

    if ($this->readCallback !== null) {
      return parent::readSession($id);
    }

    $row = (new Query())
      ->select(['data', 'expire'])
      ->from($this->sessionTable)
      ->where('[[expire]]>:expire AND [[id]]=:id', [':expire' => time(), ':id' => $id])
      ->one($this->db);

    if ($row === false) {
      return '';
    }

    $data = is_resource($row['data']) ? stream_get_contents($row['data']) : (string) $row['data'];
    $this->_readId = $id;
    $this->_readData = $data;
    $this->_readExpire = (int) $row['expire'];
    return $data;
  }

  public function writeSession($id, $data)
  {
    if (
      $this->writeCallback === null
      && $this->_readId !== null
      && $id === $this->_readId
      && $data === $this->_readData
      && ($this->_readExpire - time()) > ($this->getTimeout() - $this->touchInterval)
    ) {
      return true;
    }

    return parent::writeSession($id, $data);
  }
}
