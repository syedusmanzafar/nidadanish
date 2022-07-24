<?php

use Phinx\Migration\AbstractMigration;

class AlterCpEmFeedbackSent extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_feedback_sent");
        
        $table
            ->addColumn('in_queue', 'char', array('limit' => 1, 'null' => false, 'default' => 'N'))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_feedback_sent");

        if ($table->hasColumn('in_queue')) {
            $table->removeColumn('in_queue');
        }
    }
}
