<?php

use Phinx\Migration\AbstractMigration;

class AlterCpEmAbandCartSent extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_aband_cart_sent");
        
        $table
            ->addColumn('email', 'string', array('limit' => 255, 'null' => false, 'default' => ''))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_aband_cart_sent");

        if ($table->hasColumn('email')) {
            $table->removeColumn('email');
        }
    }
}
