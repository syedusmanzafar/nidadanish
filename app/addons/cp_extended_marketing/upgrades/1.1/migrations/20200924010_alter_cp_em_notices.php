<?php

use Phinx\Migration\AbstractMigration;

class AlterCpEmNotices extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_notices");
        
        $table
            ->addColumn('send_from', 'string', array('limit' => 256, 'null' => false))
            ->addColumn('reply_to', 'string', array('limit' => 256, 'null' => false))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_notices");

        if ($table->hasColumn('send_from')) {
            $table->removeColumn('send_from');
        }
        if ($table->hasColumn('reply_to')) {
            $table->removeColumn('reply_to');
        }
    }
}
