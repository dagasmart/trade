<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    private string $table = 'trade_log';

    /**
     * 执行迁移
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->table)) {
            //创建表
            Schema::create($this->table, function (Blueprint $table) {
                $table->comment('交易记录表');
                $table->id();
                $table->bigInteger('trade_id')->comment('交易id');
                $table->bigInteger('record_id')->comment('流水id');
                $table->string('order_no',64)->comment('订单号');
                $table->tinyInteger('trade_type')->default(1)->comment('交易类型：1付款，2退款');
                $table->integer('trade_code')->nullable()->comment('交易码');
                $table->text('trade_result')->nullable()->comment('交易结果');
                $table->dateTime('trade_time')->comment('交易时间');

                $table->index(['trade_id','record_id','order_no','trade_type','trade_code','trade_time']);

                $table->timestamps();
            });
        }
    }


    /**
     * 迁移回滚
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable($this->table)) {
            //检查是否存在数据
            $exists = DB::table($this->table)->exists();
            //不存在数据时，删除表
            if (!$exists) {
                //删除 reverse
                Schema::dropIfExists($this->table);
            }
        }
    }

};
