<aside class="left-sidebar" data-sidebarbg="skin6">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" data-sidebarbg="skin6">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.dashboard')); ?>" aria-expanded="false">
                        <i data-feather="home" class="feather-icon text-primary"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Dashboard'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.identify-form')); ?>" aria-expanded="false">
                        <i data-feather="file-text" class="feather-icon text-danger"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('KYC / Identity Form'); ?></span>
                    </a>
                </li>


                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Manage Module'); ?></span></li>

                <?php if(config('basic.top_up')): ?>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.category*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.category')); ?>" aria-expanded="false">
                            <i class="fas fa-gamepad text-success"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Game Topup'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(config('basic.voucher')): ?>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.game_voucher*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.gameVoucher')); ?>" aria-expanded="false">
                            <i class="fas fa-tag text-primary"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Voucher'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(config('basic.gift_card')): ?>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.giftCard'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.giftCard')); ?>" aria-expanded="false">

                            <i class="fab fa-cc-apple-pay text-orange"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Gift Cards'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(config('basic.sell_post')): ?>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.sellPostCategory*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.sellPostCategory')); ?>" aria-expanded="false">
                            <i class="fas fa-bullhorn text-danger"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Sell Category'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(config('basic.sell_post')): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Sell Post'); ?></span></li>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.template.show*'],3)); ?>">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <i class="fas fa-bullhorn text-warning"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Selling Post'); ?></span>
                        </a>
                        <ul aria-expanded="false"
                            class="collapse first-level base-level-line <?php echo e(menuActive([url('sell/list/*')],1)); ?>">

                            <li class="sidebar-item <?php echo e(menuActive([url('sell/list/approval')])); ?>">
                                <a class="sidebar-link <?php echo e(menuActive(url('sell/list/approval'))); ?>"
                                   href="<?php echo e(route('admin.gameSellList','approval')); ?>">
                                    <span class="hide-menu"><?php echo app('translator')->get('Approval'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item <?php echo e(menuActive([url('sell/list/pending')])); ?>">
                                <a class="sidebar-link <?php echo e(menuActive(url('sell/list/pending'))); ?>"
                                   href="<?php echo e(route('admin.gameSellList','pending')); ?>">
                                    <span class="hide-menu"><?php echo app('translator')->get('Pending'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item <?php echo e(menuActive([url('sell/list/resubmission')])); ?>">
                                <a class="sidebar-link <?php echo e(menuActive(url('sell/list/resubmission'))); ?>"
                                   href="<?php echo e(route('admin.gameSellList','resubmission')); ?>">
                                    <span class="hide-menu"><?php echo app('translator')->get('Resubmission'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item <?php echo e(menuActive([url('sell/list/hold')])); ?>">
                                <a class="sidebar-link <?php echo e(menuActive(url('sell/list/hold'))); ?>"
                                   href="<?php echo e(route('admin.gameSellList','hold')); ?>">
                                    <span class="hide-menu"><?php echo app('translator')->get('Hold'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item <?php echo e(menuActive([url('sell/list/soft-reject')])); ?>">
                                <a class="sidebar-link <?php echo e(menuActive(url('sell/list/soft-reject'))); ?>"
                                   href="<?php echo e(route('admin.gameSellList','soft-reject')); ?>">
                                    <span class="hide-menu"><?php echo app('translator')->get('Soft Rejected'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item <?php echo e(menuActive([url('sell/list/hard-reject')])); ?>">
                                <a class="sidebar-link <?php echo e(menuActive(url('sell/list/hard-reject'))); ?>"
                                   href="<?php echo e(route('admin.gameSellList','hard-reject')); ?>">
                                    <span class="hide-menu"><?php echo app('translator')->get('Hard Rejected'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>


                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('All Transaction'); ?></span></li>
                <li class="sidebar-item <?php echo e(menuActive(['admin.transaction*'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.transaction')); ?>" aria-expanded="false">
                        <i class="fas fa-exchange-alt text-danger"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Transaction Log'); ?></span>
                    </a>
                </li>

                <?php if(config('basic.top_up')): ?>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.topUpSell*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.topUpSell')); ?>" aria-expanded="false">
                            <i class="fas fa-gamepad text-success"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Topup Payment'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(config('basic.voucher')): ?>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.voucherSell*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.voucherSell')); ?>" aria-expanded="false">
                            <i class="fas fa-tag text-primary"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Voucher Payment'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(config('basic.gift_card')): ?>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.sellGiftCard','admin.giftCardSell.search'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.sellGiftCard')); ?>" aria-expanded="false">
                            <i class="fab fa-cc-apple-pay text-orange"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Gift Card Payment'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>




                <?php if(config('basic.sell_post')): ?>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.postSell','admin.postSell.search'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.postSell')); ?>" aria-expanded="false">
                            <i class="fas fa-newspaper text-danger"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Sold Post'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>






                
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Manage User'); ?></span></li>

                <li class="sidebar-item <?php echo e(menuActive(['admin.users','admin.users.search','admin.user-edit*','admin.send-email*','admin.user*'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.users')); ?>" aria-expanded="false">
                        <i class="fas fa-users text-success"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('All User'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.kyc.users.pending')); ?>"
                       aria-expanded="false">
                        <i class="fas fa-spinner text-warning"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Pending KYC'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.kyc.users')); ?>"
                       aria-expanded="false">
                        <i class="fas fa-file-invoice text-danger"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('KYC Log'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.email-send')); ?>"
                       aria-expanded="false">
                        <i class="fas fa-envelope-open text-orange"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Send Email'); ?></span>
                    </a>
                </li>

                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Payment Settings'); ?></span></li>
                <li class="sidebar-item <?php echo e(menuActive(['admin.payment.methods','admin.edit.payment.methods'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.payment.methods')); ?>"
                       aria-expanded="false">
                        <i class="fas fa-credit-card text-success"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Payment Methods'); ?></span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo e(menuActive(['admin.deposit.manual.index','admin.deposit.manual.create','admin.deposit.manual.edit'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.deposit.manual.index')); ?>"
                       aria-expanded="false">
                        <i class="fa fa-university text-info"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Manual Gateway'); ?></span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo e(menuActive(['admin.payment.pending'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.payment.pending')); ?>" aria-expanded="false">
                        <i class="fas fa-spinner text-orange"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Payment Request'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item <?php echo e(menuActive(['admin.payment.log','admin.payment.search'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.payment.log')); ?>" aria-expanded="false">
                        <i class="fas fa-history text-danger"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Payment Log'); ?></span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Payout Settings'); ?></span></li>
                <li class="sidebar-item <?php echo e(menuActive(['admin.payout-method*'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.payout-method')); ?>"
                       aria-expanded="false">
                        <i class="fas fa-credit-card text-success"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Withdraw Methods'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item <?php echo e(menuActive(['admin.payout-request'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.payout-request')); ?>" aria-expanded="false">
                        <i class="fas fa-hand-holding-usd text-danger"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Withdraw Request'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item <?php echo e(menuActive(['admin.payout-log*'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.payout-log')); ?>" aria-expanded="false">
                        <i class="fas fa-history text-orange"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Withdraw Log'); ?></span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Support Tickets'); ?></span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.ticket')); ?>" aria-expanded="false">
                        <i class="fas fa-ticket-alt text-orange"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('All Tickets'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.ticket',['open'])); ?>"
                       aria-expanded="false">
                        <i class="fas fa-spinner text-info"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Open Ticket'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.ticket',['closed'])); ?>"
                       aria-expanded="false">
                        <i class="fas fa-times-circle text-danger"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Closed Ticket'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.ticket',['answered'])); ?>"
                       aria-expanded="false">
                        <i class="fas fa-reply text-success"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Answered Ticket'); ?></span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Subscriber'); ?></span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.subscriber.index')); ?>" aria-expanded="false">
                        <i class="fas fa-envelope-open text-success"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Subscriber List'); ?></span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Controls'); ?></span></li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.basic-controls')); ?>" aria-expanded="false">
                        <i class="fas fa-cogs text-orange"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Basic Controls'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.plugin.config')); ?>" aria-expanded="false">
                        <i class="fas fa-toolbox text-danger"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Plugin Configuration'); ?></span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-envelope text-success"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Email Settings'); ?></span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level base-level-line">
                        <li class="sidebar-item">
                            <a href="<?php echo e(route('admin.email-controls')); ?>" class="sidebar-link">
                                <span class="hide-menu"><?php echo app('translator')->get('Email Controls'); ?></span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="<?php echo e(route('admin.email-template.show')); ?>" class="sidebar-link">
                                <span class="hide-menu"><?php echo app('translator')->get('Email Template'); ?> </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-mobile-alt text-info"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('SMS Settings'); ?></span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level base-level-line">
                        <li class="sidebar-item">
                            <a href="<?php echo e(route('admin.sms.config')); ?>" class="sidebar-link">
                                <span class="hide-menu"><?php echo app('translator')->get('SMS Controls'); ?></span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="<?php echo e(route('admin.sms-template')); ?>" class="sidebar-link">
                                <span class="hide-menu"><?php echo app('translator')->get('SMS Template'); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>


                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-bell text-warning"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Push Notification'); ?></span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level base-level-line">
                        <li class="sidebar-item">
                            <a href="<?php echo e(route('admin.notify-config')); ?>" class="sidebar-link">
                                <span class="hide-menu"><?php echo app('translator')->get('Configuration'); ?></span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="<?php echo e(route('admin.notify-template.show')); ?>" class="sidebar-link">
                                <span class="hide-menu"><?php echo app('translator')->get('Template'); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item <?php echo e(menuActive(['admin.language.create','admin.language.edit*','admin.language.keywordEdit*'],3)); ?>">
                    <a class="sidebar-link" href="<?php echo e(route('admin.language.index')); ?>"
                       aria-expanded="false">
                        <i class="fas fa-language text-dark"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Manage Language'); ?></span>
                    </a>
                </li>

                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Theme Settings'); ?></span></li>


                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.logo-seo')); ?>" aria-expanded="false">
                        <i class="fas fa-image text-success"></i><span
                            class="hide-menu"><?php echo app('translator')->get('Manage Logo & SEO'); ?></span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo e(route('admin.breadcrumb')); ?>" aria-expanded="false">
                        <i class="fas fa-file-image text-orange"></i><span
                            class="hide-menu"><?php echo app('translator')->get('Manage Breadcrumb'); ?></span>
                    </a>
                </li>


                <li class="sidebar-item <?php echo e(menuActive(['admin.template.show*'],3)); ?>">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-clipboard-list text-danger"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Section Heading'); ?></span>
                    </a>
                    <ul aria-expanded="false"
                        class="collapse first-level base-level-line <?php echo e(menuActive(['admin.template.show*'],1)); ?>">

                        <?php $__currentLoopData = array_diff(array_keys(config('templates')),['message','template_media']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="sidebar-item <?php echo e(menuActive(['admin.template.show'.$name])); ?>">
                                <a class="sidebar-link <?php echo e(menuActive(['admin.template.show'.$name])); ?>"
                                   href="<?php echo e(route('admin.template.show',$name)); ?>">
                                    <span class="hide-menu"><?php echo app('translator')->get(ucfirst(kebab2Title($name))); ?></span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </ul>
                </li>


                <?php
                    $segments = request()->segments();
                    $last  = end($segments);
                ?>
                <li class="sidebar-item <?php echo e(menuActive(['admin.content.create','admin.content.show*'],3)); ?>">
                    <a class="sidebar-link has-arrow <?php echo e(Request::routeIs('admin.content.show',$last) ? 'active' : ''); ?>"
                       href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-clipboard-list text-primary"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('Content Settings'); ?></span>
                    </a>
                    <ul aria-expanded="false"
                        class="collapse first-level base-level-line <?php echo e(menuActive(['admin.content.create','admin.content.show*'],1)); ?>">
                        <?php $__currentLoopData = array_diff(array_keys(config('contents')),['message','content_media']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="sidebar-item <?php echo e(($last == $name) ? 'active' : ''); ?> ">
                                <a class="sidebar-link <?php echo e(($last == $name) ? 'active' : ''); ?>"
                                   href="<?php echo e(route('admin.content.index',$name)); ?>">
                                    <span class="hide-menu"><?php echo app('translator')->get(ucfirst(kebab2Title($name))); ?></span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>

                <li class="list-divider"></li>
                <li class="text-secondary text-center mb-2">Version 2.0</li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<?php /**PATH D:\server\htdocs\gamearena\project\resources\views/admin/layouts/sidebar.blade.php ENDPATH**/ ?>