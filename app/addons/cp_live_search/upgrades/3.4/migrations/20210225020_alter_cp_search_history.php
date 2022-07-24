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
            ->changeColumn('search_id', 'integer', array('limit' => MysqlAdapter::INT_REGULAR, 'signed' => false, 'null' => false, 'identity' => true))
            ->save();
    }

    public function down()
    {
        
    }
}
