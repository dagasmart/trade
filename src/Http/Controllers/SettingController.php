<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\TradeService;
use Illuminate\Http\Request;

class SettingController extends AdminController
{
    protected string $serviceName = TradeService::class;

    public function index()
    {
        if ($this->actionOfGetData()) return $this->response()->success(settings()->all());

        $page = $this->basePage()->body([
            amis()->Alert()
                ->showIcon()
                ->style([
                    'padding' => '1rem',
                    'color' => 'var(--colors-brand-6)',
                    'border-style' => 'dashed',
                    'border-color' => 'var(--colors-brand-6)',
                    'background-color' => 'var(--Tree-item-onChekced-bg)',
                ])->body("基本设置，站点名称、logo标识"),
            $this->form(),
        ]);

        return $this->response()->success($page);
    }

    public function form()
    {
        return $this->baseForm(false)
            ->redirect('')
            ->api($this->getStorePath())
            ->initApi('/system/settings?_action=getData')
            ->body(
                amis()->Tabs()->name('settings')->tabs([
//                    amis()->Tab()->title('基本设置')->body([
//                        amis()->TextControl()->label('网站名称')->name('site_name'),
//                        amis()->InputKV()->label('附加配置')->name('addition_config'),
//                    ])->value('basic'),
                    amis()->Tab()->title('支付设置')->body([
                        // line | card | radio | vertical | chrome | simple | strong | tiled | sidebar
                        amis()->Tabs()->tabsMode('sidebar')->tabs([
                            amis()->Tab()->title('支付宝')->body([
                                amis()->Alert()
                                    ->showIcon()
                                    ->style([
                                        'padding' => '1rem',
                                        'color' => 'var(--colors-brand-6)',
                                        'border-style' => 'dashed',
                                        'border-color' => 'var(--colors-brand-6)',
                                        'background-color' => 'var(--Tree-item-onChekced-bg)',
                                    ])->body("支付宝小程序、移动端必须关联支付宝商户，否则支付无效"),
                                amis()->GroupControl()->body([
                                    amis()->Tabs()->tabsMode('line')->tabs([
                                        amis()->Tab()->title('支付商户')->body([
                                            amis()->TextControl('payment.alipay.app_id','商户 app_id')
                                                ->description('必填，-支付宝分配的 app_id')
                                                ->labelRemark('提示：服务商模式下为服务商户号')
                                                ->size('lg'),
                                            amis()->TextControl('payment.alipay.app_secret_cert','商户秘钥 app_secret_cert')
                                                ->description('必填，应用私钥，字符串或路径')
                                                ->size('lg'),
                                        ]),
                                        amis()->Tab()->title('支付证书')->body([
                                            amis()->FileControl('payment.alipay.app_public_cert_path','应用公钥证书 app_secret_cert')
                                                ->description('必填，应用公钥证书 路径')
                                                ->size('lg'),
                                            amis()->FileControl('payment.alipay.alipay_public_cert_path','支付宝公钥证书 alipay_public_cert_path')
                                                ->description('必填，支付宝公钥证书 路径')
                                                ->size('lg'),
                                            amis()->FileControl('payment.alipay.alipay_public_cert_path','支付宝根证书 alipay_root_cert_path')
                                                ->description('必填，支付宝根证书 路径')
                                                ->size('lg'),
                                        ]),
                                        amis()->Tab()->title('支付回调')->body([
                                            amis()->TextControl('payment.alipay.return_url','回调结果 return')
                                                ->description('选填，支付回调结果，return地址')
                                                ->size('lg'),
                                            amis()->TextControl('payment.alipay.notify_url','回调处理 notify')
                                                ->description('必填，支付回调处理，return地址')
                                                ->size('lg'),
                                        ]),
                                        amis()->Tab()->title('服务商')->body([
                                            amis()->TextControl('payment.alipay.service_provider_id','服务商户号')
                                                ->description('选填，服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数')
                                                ->size('lg'),
                                            amis()->RadiosControl('payment.alipay.mode','环境模式')
                                                ->description('选填，默认为正常模式。可选为： MODE_NORMAL-正常, MODE_SANDBOX-沙箱, MODE_SERVICE-服务商')
                                                ->options($this->service->modeOption())
                                                ->value( 0)
                                                ->size('lg'),
                                        ]),
                                    ]),
                                ]),
                            ])->className(['p-10' => true]),

                            amis()->Tab()->title('微信支付')->mode('normal')->body([
                                amis()->Alert()
                                    ->showIcon()
                                    ->style([
                                        'padding' => '1rem',
                                        'color' => 'var(--colors-brand-6)',
                                        'border-style' => 'dashed',
                                        'border-color' => 'var(--colors-brand-6)',
                                        'background-color' => 'var(--Tree-item-onChekced-bg)',
                                    ])->body("微信小程序、公众号、移动端必须关联微信支付，否则支付无效"),
                                amis()->GroupControl()->body([
                                    amis()->Tabs()->tabsMode('line')->tabs([
                                        amis()->Tab()->title('商户信息')->body([
                                            amis()->TextControl('payment.wechat.mch_id','商户 app_id')
                                                ->description('必填，服务商模式下为服务商户号')
                                                ->labelRemark('提示：服务商模式下为服务商户号')
                                                ->size('lg'),
                                            amis()->TextControl('payment.wechat.mch_secret_key','应用私钥 app_secret_cert')
                                                ->description('必填，应用私钥 字符串或路径')
                                                ->size('lg'),
                                            amis()->GroupControl()->body([
                                                amis()->TextControl('payment.wechat.mch_secret_cert','商户私钥 apiclient_key')
                                                    ->description('必填，商户私钥为32位加密字符串或证书路径')
                                                    ->size('lg'),
                                                amis()->FileControl('payment.wechat.mch_secret_cert',' ')
                                            ]),
                                            amis()->GroupControl()->body([
                                                amis()->TextControl('payment.wechat.mch_public_cert_path','商户公钥 pub_key')
                                                    ->description('必填，商户公钥为32位加密字符串或证书路径')
                                                    ->size('lg'),
                                                amis()->FileControl('payment.wechat.mch_public_cert_path',' ')
                                            ]),
                                        ]),
                                        amis()->Tab()->title('小程序')->body([
                                            amis()->TextControl('payment.wechat.mini_app_id','小程序 app_id')
                                                ->description('选填，小程序的app_id')
                                                ->size('lg'),
                                            amis()->TextControl('payment.wechat.mini_secret_key','小程序秘钥 secret_key')
                                                ->description('必填，小程序秘钥')
                                                ->size('lg'),
                                        ]),
                                        amis()->Tab()->title('公众号')->body([
                                            amis()->TextControl('payment.wechat.mp_app_id','公众号 app_id')
                                                ->description('选填，公众号的app_id')
                                                ->size('lg'),
                                            amis()->TextControl('payment.wechat.mp_secret_key','公众号秘钥 secret_key')
                                                ->description('必填，公众号秘钥')
                                                ->size('lg'),
                                        ]),
                                        amis()->Tab()->title('移动端')->body([
                                            amis()->TextControl('payment.wechat.app_id','app移动端 app_id')
                                                ->description('选填，app移动终端的 app_id')
                                                ->size('lg'),
                                        ]),
                                    ]),
                                ]),
                            ])->className(['p-10' => true]),
                            amis()->Tab()->title('抖音支付')->body([
                                amis()->TextControl()->label('网站名称')->name('site_name'),
                            ]),
                            amis()->Tab()->title('银联支付')->body([
                                amis()->TextControl()->label('网站名称')->name('site_name'),
                            ]),
                        ]),
                    ])->value('payment'),
//                    amis()->Tab()->title('上传设置')->body([
//                        amis()->TextControl()->label('上传域名')->name('upload.domain'),
//                        amis()->TextControl()->label('上传路径')->name('upload.path'),
//                    ])->value('upload'),
                ])
            );
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'site_name',
            'addition_config',
            'upload_domain',
            'upload_path',
        ]);

        return settings()->adminSetMany($data);
    }
}
