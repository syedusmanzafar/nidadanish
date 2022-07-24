<?php

use Phinx\Migration\AbstractMigration;

class CreateCpCustomLogs extends AbstractMigration
{     
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_custom_logs",
            array('id' => false, 'primary_key' => 'log_id', 'engine' => 'MyISAM')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('log_id', 'integer', array('signed' => false, 'null' => false, 'identity' => true))
            ->addColumn('user_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('company_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('timestamp', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('section', 'string', array('limit' => 32, 'null' => false))
            ->addColumn('action', 'string', array('limit' => 64, 'null' => false))
            ->addColumn('content', 'text', array('null' => false))
            ->addColumn('extra', 'text', array('null' => false))
            ->addIndex(array('company_id', 'section'))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_custom_logs");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
