<?php

use Phinx\Migration\AbstractMigration;

class CreateCpSearchMotivation extends AbstractMigration
{     
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_search_motivation",
            array('id' => false, 'primary_key' => array('object_type', 'object_id', 'lang_code', 'company_id'), 'engine' => 'MyISAM')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('object_type', 'char', array('limit' => 1, 'null' => false, 'default' => 'D'))
            ->addColumn('object_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('company_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('lang_code', 'char', array('limit' => 2, 'null' => false))
            ->addColumn('content', 'text', array('null' => false))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_motivation");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
