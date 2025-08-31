<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    private string $table = 'trade_order';

    /**
     * 执行迁移
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->table)) {
            //创建表
            Schema::create($this->table, function (Blueprint $table) {
                $table->comment('交易账单表');
                $table->id();

                $table->integer('order_id')->comment('订单id');
                $table->string('order_no',64)->comment('订单号');
                $table->string('base_order_no',64)->nullable()->comment('原始订单号');
                $table->string('order_source',20)->nullable()->comment('订单来源：shop商城，soft软件，recharge充值');
                $table->tinyInteger('trade_type')->default(1)->comment('交易类型：1支付，2退款');
                $table->string('trade_channel',20)->nullable()->comment('交易渠道：alipay支付宝、wechat微信、douyin抖音、unipay银联');
                $table->decimal('trade_amount')->nullable()->comment('交易金额');
                $table->decimal('refund_amount')->nullable()->comment('退款金额');
                $table->tinyInteger('trade_status')->default(0)->comment('交易状态：0待付款，1已付款，-1已退款，-2部分退款');
                $table->timestamp('trade_time')->nullable()->comment('交易时间');
                $table->string('trade_no',64)->nullable()->comment('交易号');
                $table->string('trade_code',10)->nullable()->comment('交易码：10000-支付成功');
                $table->jsonb('trade_result')->nullable()->comment('交易结果');
                $table->tinyInteger('is_plat')->default(0)->comment('是否平台订单');
                $table->bigInteger('payer_id')->comment('付款人id');
                $table->json('payer')->comment('付款人信息');

                $table->string('module', 50)->nullable()->comment('模块');
                $table->bigInteger('mer_id')->nullable()->comment('商户id');

                $table->index(['id','order_id','order_no','base_order_no','trade_no','trade_time']);
                $table->unique(['order_no']);

                $table->timestamps();
                $table->softDeletes();
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
