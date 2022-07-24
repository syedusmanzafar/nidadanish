<?php

use Phinx\Migration\AbstractMigration;

class AlterUsers extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}users");

        $table
            ->addColumn('cp_ac_activation', 'char', array('limit' => 1, 'null' => false, 'default' => 'N'))
            ->addColumn('cp_ac_hash', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}users");

        if ($table->hasColumn('cp_ac_activation')) {
            $table->removeColumn('cp_ac_activation');
        }
        if ($table->hasColumn('cp_ac_hash')) {
            $table->removeColumn('cp_ac_hash');
        }
    }
}
