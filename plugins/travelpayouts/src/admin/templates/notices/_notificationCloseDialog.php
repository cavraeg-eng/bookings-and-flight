<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */
?>

<div class="bp3-portal travelpayouts-notice-dialog" id="tp-notification-disable-dialog" aria-hidden="true">
    <div class="bp3-overlay-backdrop bp3-overlay-appear-done bp3-overlay-enter-done"
         tabindex="-1"
         data-micromodal-close>
        <div class="bp3-dialog-container bp3-overlay-content bp3-overlay-appear-done bp3-overlay-enter-done">
            <div class="bp3-dialog" role="dialog" aria-modal="true">
                <header class="bp3-dialog-header">
                    <h4 class="bp3-heading">
                        <?= Travelpayouts::__('Want to hide this notification?') ?>
                    </h4>
                    <button
                            tabindex="10"
                            class="bp3-button bp3-minimal bp3-dialog-close-button"
                            data-micromodal-close>

                    <span icon="small-cross" class="bp3-icon bp3-icon-small-cross" data-micromodal-close>
                        <svg data-icon="small-cross" width="20" height="20" viewBox="0 0 20 20" data-micromodal-close>
                            <path d="M11.41 10l3.29-3.29c.19-.18.3-.43.3-.71a1.003 1.003 0 00-1.71-.71L10 8.59l-3.29-3.3a1.003 1.003 0 00-1.42 1.42L8.59 10 5.3 13.29c-.19.18-.3.43-.3.71a1.003 1.003 0 001.71.71l3.29-3.3 3.29 3.29c.18.19.43.3.71.3a1.003 1.003 0 00.71-1.71L11.41 10z"
                                  fill-rule="evenodd"></path>
                        </svg>
                    </span>
                    </button>
                    </header>
                    <main class="bp3-dialog-body" id="modal-1-content">
                        <p>
                            <?= Travelpayouts::__("If you'd like to hide this notification and keep receiving future notifications, click 'Hide Notification'. If you'd like to turn off notifications permanently, click 'Turn Notifications Off'.") ?>
                        </p>
                    </main>
                    <footer class="bp3-dialog-footer">
                        <div class="bp3-dialog-footer-actions">
                            <button type="button" class="bp3-button" data-action="disable">
                                <?= Travelpayouts::__('Turn Notifications Off') ?>
                            </button>
                            <button type="submit" class="bp3-button bp3-intent-primary" data-action="hide">
                                <?= Travelpayouts::__('Hide Notification') ?>
                            </button>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>


