<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AlterCpSearchHistory extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_history");
        
        if(!$table->exists()) {
            return;
        }

        $table
            ->addColumn('storefront_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];
        $table = $this->table("{$pr}cp_search_history");

        if ($table->hasColumn('storefront_id')) {
            $table->removeColumn('storefront_id');
        }
    }
}
