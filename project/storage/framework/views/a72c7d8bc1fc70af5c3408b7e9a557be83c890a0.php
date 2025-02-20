<!-- notification panel -->
<div class="notification-panel" id="pushNotificationArea">
    <button class="notification-icon">
        <img src="<?php echo e(asset($themeTrue) .'/images/icon/notification.png'); ?>" alt="..."/>
        <span v-if="items.length > 0" class="badge">{{ items.length }}</span>
    </button>
    <div class="notifications py-4">
        <div v-if="items.length > 0" class="notification-box">
            <div  v-for="(item, index) in items"  class="notification d-flex align-items-top">
                <div class="img-box">
                    <i :class="item.description.icon" ></i>
                </div>
                <div>
                    <a href="javascript:void(0)" @click.prevent="readAt(item.id, item.description.link)"
                       v-cloak v-html="item.description.text"></a>
                    <span v-cloak>{{ item.formatted_date }}</span>
                </div>
            </div>

        </div>

        <a class="clear-notification"  v-if="items.length > 0" @click.prevent="readAll" href="javascript:void(0)"><?php echo app('translator')->get('Clear'); ?></a>
        <a class="clear-notification "  v-if="items.length == 0" href="javascript:void(0)"><?php echo app('translator')->get('You have no notifications'); ?></a>
    </div>
</div>

<?php $__env->startPush('script'); ?>
    <?php if(auth()->guard()->check()): ?>
        <script>
            'use strict';
            let pushNotificationArea = new Vue({
                el: "#pushNotificationArea",
                data: {
                    items: [],
                },
                mounted() {
                    this.getNotifications();
                    this.pushNewItem();
                },
                methods: {
                    getNotifications() {
                        let app = this;
                        axios.get("<?php echo e(route('user.push.notification.show')); ?>")
                            .then(function (res) {
                                app.items = res.data;
                            })
                    },
                    readAt(id, link) {
                        let app = this;
                        let url = "<?php echo e(route('user.push.notification.readAt', 0)); ?>";
                        url = url.replace(/.$/, id);
                        axios.get(url)
                            .then(function (res) {
                                if (res.status) {
                                    app.getNotifications();
                                    if (link != '#') {
                                        window.location.href = link
                                    }
                                }
                            })
                    },
                    readAll() {
                        let app = this;
                        let url = "<?php echo e(route('user.push.notification.readAll')); ?>";
                        axios.get(url)
                            .then(function (res) {
                                if (res.status) {
                                    app.items = [];
                                }
                            })
                    },
                    pushNewItem() {
                        let app = this;
                        // Pusher.logToConsole = true;
                        let pusher = new Pusher("<?php echo e(env('PUSHER_APP_KEY')); ?>", {
                            encrypted: true,
                            cluster: "<?php echo e(env('PUSHER_APP_CLUSTER')); ?>"
                        });
                        let channel = pusher.subscribe('user-notification.' + "<?php echo e(Auth::id()); ?>");
                        channel.bind('App\\Events\\UserNotification', function (data) {
                            app.items.unshift(data.message);
                        });
                        channel.bind('App\\Events\\UpdateUserNotification', function (data) {
                            app.getNotifications();
                        });
                    }
                }
            });
        </script>
    <?php endif; ?>

<?php $__env->stopPush(); ?>
<?php /**PATH D:\server\htdocs\gamearena\project\resources\views/themes/gameshop/partials/pushNotify.blade.php ENDPATH**/ ?>