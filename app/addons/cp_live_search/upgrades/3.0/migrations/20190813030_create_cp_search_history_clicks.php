<?php

use Phinx\Migration\AbstractMigration;

class CreateCpSearchHistoryClicks extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_search_history_clicks",
            array('id' => false, 'primary_key' => array('search_id', 'product_id'), 'engine' => 'MyISAM')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('search_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('product_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_history_clicks");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
