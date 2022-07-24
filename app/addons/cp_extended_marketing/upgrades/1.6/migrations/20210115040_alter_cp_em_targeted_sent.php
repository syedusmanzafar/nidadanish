<?php

use Phinx\Migration\AbstractMigration;

class AlterCpEmTargetedSent extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_targeted_sent");
        
        $table
            ->addColumn('in_queue', 'char', array('limit' => 1, 'null' => false, 'default' => 'N'))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_targeted_sent");

        if ($table->hasColumn('in_queue')) {
            $table->removeColumn('in_queue');
        }
    }
}
