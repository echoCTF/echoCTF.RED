<?php

use yii\db\Migration;

/**
 * Class m220425_165747_alter_table_stream_modify_column_ts_precision
 */
class m220425_165747_alter_table_stream_modify_column_ts_precision extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->alterColumn('stream', 'ts', 'timestamp(4) not null default current_timestamp');
    }

    public function down()
    {
        $this->alterColumn('stream', 'ts', 'timestamp() not null default current_timestamp');
    }
}
