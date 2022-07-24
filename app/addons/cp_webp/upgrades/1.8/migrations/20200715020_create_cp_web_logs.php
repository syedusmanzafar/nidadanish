<?php

use Phinx\Migration\AbstractMigration;

class CreateCpWebLogs extends AbstractMigration
{     
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_web_logs",
            array('id' => false, 'primary_key' => 'log_id', 'engine' => 'InnoDB')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('log_id', 'integer', array('signed' => false, 'null' => false, 'identity' => true))
            ->addColumn('start_time', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('end_time', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('info', 'text', array('null' => false))
            ->addColumn('type', 'char', array('limit' => 1, 'null' => false, 'default' => 'N'))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_logs");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
