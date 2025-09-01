<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    private string $table = 'trade_order_log';

    /**
     * 执行迁移
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->table)) {
            //创建表
            Schema::create($this->table, function (Blueprint $table) {
                $table->comment('交易流水表');
                $table->id();

                $table->integer('trade_id')->comment('交易id');
                $table->string('order_no',64)->comment('订单号');
                $table->tinyInteger('trade_type')->default(1)->comment('交易类型：1支付，2退款');
                $table->string('trade_channel',20)->nullable()->comment('交易渠道：alipay支付宝、wechat微信、douyin抖音、unipay银联');
                $table->decimal('trade_amount')->nullable()->comment('金额');
                $table->tinyInteger('trade_status')->default(0)->comment('交易状态：0待付款，1已付款，-1已退款，-2部分退款');
                $table->string('trade_code',10)->nullable()->comment('交易码：10000-支付成功');
                $table->jsonb('trade_result')->nullable()->comment('交易结果');
                $table->string('opera_type',10)->nullable()->comment('操作类别，user用户，mer商户，plat平台');
                $table->bigInteger('opera_id')->comment('操作人id');
                $table->json('opera')->comment('操作人信息');

                $table->string('module', 50)->nullable()->comment('模块');
                $table->bigInteger('mer_id')->nullable()->comment('商户id');

                $table->index(['id','trade_id','order_no','created_at','opera_id']);
                $table->unique(['order_no']);

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
