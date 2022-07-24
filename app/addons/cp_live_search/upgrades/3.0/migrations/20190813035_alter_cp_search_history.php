<?php

use Phinx\Migration\AbstractMigration;

class AlterCpSearchHistory extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_history");

        $table
            ->addColumn('company_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('lang_code', 'char', array('limit' => 2, 'null' => false))
            ->addIndex(array('company_id'))
            ->addIndex(array('lang_code'))
            ->addIndex(array('search'))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_history");

        if ($table->hasColumn('company_id')) {
            $table->removeColumn('company_id');
        }
    }
}
