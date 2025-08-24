<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\TradeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingController extends AdminController
{
    protected string $serviceName = TradeService::class;

    public function index()
    {
        if ($this->actionOfGetData()){
            return $this->response()->success(['payment' => settings()->pay('payment')]);
        }

        $page = $this->basePage()->body([
            amis()->Alert()
                ->showIcon()
                ->showCloseButton()
                ->closeButtonClassName(['text-xs'=>true])
                ->style([
                    'padding' => '1rem',
                    'color' => 'var(--colors-brand-6)',
                    'border-style' => 'dashed',
                    'border-color' => 'var(--colors-brand-6)',
                    'background-color' => 'var(--Tree-item-onChekced-bg)',
                    'opacity' => 0.65,
                ])->body("
                    注意事项：
                    </br>商户私钥 必须严格保密，切勿泄露。
                    </br>确保密钥配置正确，否则支付接口无法正常使用
                    </br>如果使用 证书模式，需下载证书文件并配置到服务器
                "),
            $this->form(),
        ]);

        return $this->response()->success($page);
    }

    public function form()
    {
        return $this->baseForm(true)
            ->redirect('')
            ->data(['admin_link' =>admin_link()])
            ->api($this->getStorePath())
            ->initApi(admin_url('trade/settings?_action=getData'))
            ->body(
                amis()->Tabs()->name('settings')->tabs([
                    amis()->Tab()->title('支付设置')->body([
                        // line | card | radio | vertical | chrome | simple | strong | tiled | sidebar
                        amis()->Tabs()->tabsMode('sidebar')->linksClassName(['p-6' => true])->tabs([
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
                                        amis()->Tab()->title('商户信息')->body([
                                            amis()->TextControl('payment.alipay.default.app_id','商户号')
                                                ->description('必填，-支付宝分配的 app_id')
                                                ->labelRemark('提示：服务商模式下为服务商户号')
                                                ->size('lg'),
                                            amis()->TextareaControl('payment.alipay.default.app_secret_cert','商户秘钥')
                                                ->description('必填，应用私钥，字符串或路径 app_secret_cert')
                                                ->maxRows(12)
                                                ->minRows(12)
                                                ->size(),
                                        ]),
                                        amis()->Tab()->title('支付证书')->body([
                                            amis()->FileControl('payment.alipay.default.app_public_cert_path','应用公钥证书')
                                                ->description('必填，应用公钥证书 路径 appCertPublicKey')
                                                ->receiver(admin_url('upload_cert?channel=alipay'))
                                                ->accept('.crt')
                                                ->size('lg'),
                                            amis()->FileControl('payment.alipay.default.alipay_public_cert_path','支付宝公钥证书')
                                                ->description('必填，支付宝公钥证书 路径 alipayCertPublicKey_RSA2')
                                                ->receiver(admin_url('upload_cert?channel=alipay'))
                                                ->accept('.crt')
                                                ->size('lg'),
                                            amis()->FileControl('payment.alipay.default.alipay_root_cert_path','支付宝根证书')
                                                ->description('必填，支付宝根证书 路径 alipayRootCert')
                                                ->receiver(admin_url('upload_cert?channel=alipay'))
                                                ->accept('.crt')
                                                ->size('lg'),
                                        ]),
                                        amis()->Tab()->title('支付回调')->body([
                                            amis()->TextControl('payment.alipay.default.return_url','同步回调地址')
                                                ->description('系统自动生成同步回调return_url地址')
                                                ->static()
                                                ->size(),
                                            amis()->TextControl('payment.alipay.default.notify_url','异步回调地址')
                                                ->description('系统自动生成异步回调notify_url地址')
                                                ->static()
                                                ->size(),
                                        ]),
                                        amis()->Tab()->title('服务商')->body([
                                            amis()->TextControl('payment.alipay.default.service_provider_id','服务商户号')
                                                ->description('选填，服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数')
                                                ->size('lg'),
                                            amis()->RadiosControl('payment.alipay.default.mode','环境模式')
                                                ->description('选填，默认为正常模式。可选为： MODE_NORMAL-正常, MODE_SANDBOX-沙箱, MODE_SERVICE-服务商')
                                                ->options($this->service->modeOption())
                                                ->value( 0)
                                                ->size('lg'),
                                        ]),
                                    ])->toolbar([
                                        amis()->SwitchControl('payment.alipay.default.switch')->size('sm')
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
                                            amis()->TextControl('payment.wechat.default.mch_id','商户号')
                                                ->description('必填，服务商模式下为服务商户号 app_id')
                                                ->labelRemark('提示：服务商模式下为服务商户号')
                                                ->size('lg'),
                                            amis()->TextControl('payment.wechat.default.mch_secret_key','应用私钥')
                                                ->description('必填，应用私钥 字符串或路径 app_secret_cert')
                                                ->size('lg'),
                                            amis()->GroupControl()->body([
                                                amis()->TextControl('payment.wechat.default.mch_secret_cert','商户私钥')
                                                    ->description('必填，商户私钥为32位加密字符串或证书路径 apiclient_key')
                                                    ->size('lg'),
                                                amis()->FileControl('payment.wechat.default.mch_secret_cert',' ')
                                            ]),
                                            amis()->GroupControl()->body([
                                                amis()->TextControl('payment.wechat.default.mch_public_cert_path','商户公钥')
                                                    ->description('必填，商户公钥为32位加密字符串或证书路径 pub_key')
                                                    ->size('lg'),
                                                amis()->FileControl('payment.wechat.default.mch_public_cert_path',' ')
                                            ]),
                                        ]),
                                        amis()->Tab()->title('小程序')->body([
                                            amis()->TextControl('payment.wechat.default.mini_app_id','小程序 app_id')
                                                ->description('选填，小程序的app_id')
                                                ->size('lg'),
                                            amis()->TextControl('payment.wechat.default.mini_secret_key','小程序秘钥')
                                                ->description('必填，小程序秘钥 secret_key')
                                                ->size('lg'),
                                        ]),
                                        amis()->Tab()->title('公众号')->body([
                                            amis()->TextControl('payment.wechat.default.mp_app_id','公众号 app_id')
                                                ->description('选填，公众号的app_id')
                                                ->size('lg'),
                                            amis()->TextControl('payment.wechat.default.mp_secret_key','公众号秘钥')
                                                ->description('必填，公众号秘钥secret_key')
                                                ->size('lg'),
                                        ]),
                                        amis()->Tab()->title('移动端')->body([
                                            amis()->TextControl('payment.wechat.default.app_id','app移动端')
                                                ->description('选填，app移动终端的 app_id')
                                                ->size('lg'),
                                        ]),
                                    ])->toolbar([
                                        amis()->SwitchControl('payment.wechat.default.switch')->size('sm')
                                    ]),
                                ]),
                            ])->className(['p-10' => true]),

                            amis()->Tab()->title('抖音支付')->mode('normal')->body([
                                amis()->Alert()
                                    ->showIcon()
                                    ->style([
                                        'padding' => '1rem',
                                        'color' => 'var(--colors-brand-6)',
                                        'border-style' => 'dashed',
                                        'border-color' => 'var(--colors-brand-6)',
                                        'background-color' => 'var(--Tree-item-onChekced-bg)',
                                    ])->body("抖音支付只支持抖音小程序"),
                                amis()->GroupControl()->body([
                                    amis()->Tabs()->tabsMode('line')->tabs([
                                        amis()->Tab()->title('商户信息')->body([
                                            amis()->TextControl('payment.douyin.default.mch_id','商户号')
                                                ->description('必填，服务商模式下为服务商户号 app_id')
                                                ->labelRemark('提示：服务商模式下为服务商户号')
                                                ->size('lg'),
                                            amis()->TextControl('payment.douyin.default.mch_secret_key','应用私钥')
                                                ->description('必填，应用私钥 字符串或路径 app_secret_cert')
                                                ->size('lg'),
                                            amis()->GroupControl()->body([
                                                amis()->TextControl('payment.douyin.default.mch_secret_cert','商户私钥')
                                                    ->description('必填，商户私钥为32位加密字符串或证书路径 apiclient_key')
                                                    ->size('lg'),
                                                amis()->FileControl('payment.douyin.default.mch_secret_cert',' ')
                                            ]),
                                            amis()->GroupControl()->body([
                                                amis()->TextControl('payment.douyin.default.mch_public_cert_path','商户公钥')
                                                    ->description('必填，商户公钥为32位加密字符串或证书路径 pub_key')
                                                    ->size('lg'),
                                                amis()->FileControl('payment.douyin.default.mch_public_cert_path',' ')
                                            ]),
                                        ]),
                                        amis()->Tab()->title('小程序')->body([
                                            amis()->TextControl('payment.douyin.default.mini_app_id','小程序 app_id')
                                                ->description('选填，小程序的app_id')
                                                ->size('lg'),
                                            amis()->TextControl('payment.douyin.default.mini_secret_key','小程序秘钥 secret_key')
                                                ->description('必填，小程序秘钥')
                                                ->size('lg'),
                                        ]),
                                    ])->toolbar([
                                        amis()->SwitchControl('payment.douyin.default.switch')->size('sm')
                                    ]),
                                ]),
                            ])->className(['p-10' => true]),

                            amis()->Tab()->title('银联支付')->mode('normal')->body([
                                amis()->Alert()
                                    ->showIcon()
                                    ->style([
                                        'padding' => '1rem',
                                        'color' => 'var(--colors-brand-6)',
                                        'border-style' => 'dashed',
                                        'border-color' => 'var(--colors-brand-6)',
                                        'background-color' => 'var(--Tree-item-onChekced-bg)',
                                    ])->body("银联支付必须开通银联商户，否则支付无效"),
                                amis()->GroupControl()->mode('horizontal')->body([
                                    amis()->Tabs()->tabsMode('line')->tabs([
                                        amis()->Tab()->title('商户信息')->body([
                                            amis()->TextControl('payment.unipay.default.mch_id','商户号')
                                                ->description('必填，商户号 app_id')
                                                ->labelRemark('提示：服务商模式下为服务商户号')
                                                ->size('lg')
                                                ->required(false),
                                            amis()->TextControl('payment.unipay.default.mch_secret_key','应用私钥')
                                                ->description('选填，商户密钥 app_secret_cert，为银联条码支付综合前置平台配置：https://up.95516.com/open/openapi?code=unionpay')
                                                ->size('lg')
                                                ->required(false),
                                            amis()->TextControl('payment.unipay.default.mch_cert_path','商户公私钥')
                                                ->description('必填，商户公私钥 mch_cert_path')
                                                ->size('lg')
                                                ->required(false),
                                            amis()->TextControl('payment.unipay.default.mch_cert_password','商户公私钥密码')
                                                ->description('必填，商户公私钥密码 mch_cert_password')
                                                ->size('lg')
                                                ->required(false),
                                            amis()->FileControl('payment.unipay.default.unipay_public_cert_path','银联公钥证书')
                                                ->description('必填，银联公钥证书路径 unipay_public_cert_path')
                                                ->size('lg')
                                                ->required(false),
                                            amis()->TextControl('payment.unipay.default.return_url','同步跳转地址')
                                                ->description('必填，商户公私钥密码 return_url')
                                                ->size('lg')
                                                ->required(false),
                                            amis()->TextControl('payment.unipay.default.notify_url','异步回调地址')
                                                ->description('必填，回调地址 notify_url')
                                                ->size('lg')
                                                ->required(false),
                                        ]),
                                    ])->toolbar([
                                        amis()->SwitchControl('payment.unipay.default.switch')->size('sm')
                                    ]),
                                ]),
                            ])->className(['p-10' => true]),
                        ]),
                    ])->value('payment'),
                ])->toolbar([
                    amis()->SwitchControl('payment.switch')
                        ->onText('开启')
                        ->offText('关闭')
                        ->size('xs'),
                ])
            );
    }


    public function store(Request $request): JsonResponse|JsonResource
    {
        $data = $request->only([
            'site_name',
            'addition_config',
            'payment'
        ]);

        $payment = $data['payment'] ?? null;
        if ($data && $payment) {
            $alipay = $payment['alipay'] ?? null;
            if ($alipay) {
                $alipay_default = $alipay['default'] ?? null;
                if ($alipay_default) {
                    $data['payment']['alipay']['default']['return_url'] = admin_link('trade/return/alipay');
                    $data['payment']['alipay']['default']['notify_url'] = admin_link('trade/notify/alipay');
                }
            }

            $wechat = $payment['wechat'] ?? null;
            if ($wechat) {
                $wechat_default = $wechat['default'] ?? null;
                if ($wechat_default) {
                    $data['payment']['wechat']['default']['notify_url'] = admin_link('trade/notify/wechat');
                }
            }

            $douyin = $payment['douyin'] ?? null;
            if ($douyin) {
                $douyin_default = $douyin['default'] ?? null;
                if ($douyin_default) {
                    $data['payment']['douyin']['default']['notify_url'] = admin_link('trade/notify/douyin');
                }
            }

            $unipay = $payment['unipay'] ?? null;
            if ($unipay) {
                $unipay_default = $unipay['default'] ?? null;
                if ($unipay_default) {
                    $data['payment']['unipay']['default']['return_url'] = admin_link('trade/return/unipay');
                    $data['payment']['unipay']['default']['notify_url'] = admin_link('trade/notify/unipay');
                }
            }
        }
        return settings()->adminSetMany($data);
    }
}
