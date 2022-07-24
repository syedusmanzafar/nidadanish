<?php

use Phinx\Migration\AbstractMigration;

class AlterCpEmViewedSent extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_viewed_sent");
        
        $table
            ->addColumn('product_ids', 'text', array('null' => false))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_viewed_sent");

        if ($table->hasColumn('product_ids')) {
            $table->removeColumn('product_ids');
        }
    }
}
