<?php

use Phinx\Migration\AbstractMigration;

class CreateCpEmSendQueue extends AbstractMigration
{     
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_em_send_queue",
            array('id' => false, 'primary_key' => 'queue_id', 'engine' => 'InnoDB')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('queue_id', 'integer', array('signed' => false, 'null' => false, 'identity' => true))
            ->addColumn('notice_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('user_data', 'blob', array('limit' => 4294967295))
            ->addColumn('mail_data', 'blob', array('limit' => 4294967295))
            ->addColumn('rendered_data_hid', 'blob', array('limit' => 4294967295))
            ->addColumn('timestamp', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addIndex(array('notice_id'))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_send_queue");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
