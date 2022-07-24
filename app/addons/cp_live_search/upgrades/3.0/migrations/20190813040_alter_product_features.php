<?php

use Phinx\Migration\AbstractMigration;

class AlterProductFeatures extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}product_features");
        if (!$table->hasColumn('cp_ls_use')) {
            $table
                ->addColumn('cp_ls_use', 'char', array('limit' => 1, 'null' => false, 'default' => 'N'))
                ->save();
        }
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}product_features");

        if ($table->hasColumn('cp_ls_use')) {
            $table->removeColumn('cp_ls_use');
        }
    }
}
