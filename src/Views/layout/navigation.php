<?php
/**
 * @var Models\Session $activeSession
 * @var Models\TranslationManager $translator
 */
?>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-0">
        <div>
            <h1 class="navbar-brand mb-0"><?= $translator->get('heading_app') ?></h1>
            <?php if ($activeSession): ?>
            <div class="navbar-text" id="currentSessionInfo">
                <?= $translator->get('label_session', ['name' => htmlspecialchars($activeSession->name)]) ?>
            </div>
            <?php endif; ?>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <button class="btn btn-success nav-link" data-bs-toggle="modal" data-bs-target="#addRunModal" <?php echo !$activeSession ? 'disabled' : ''; ?>>
                        <?= $translator->get('button_add_run') ?>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <button class="btn nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <?= $translator->get('label_session', ['name' => '']) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <?php if ($activeSession): ?>
                        <li>
                            <button class="dropdown-item" id="editSession">
                                <?= $translator->get('button_edit_session') ?>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" id="completeSession">
                                <?= $translator->get('button_complete_session') ?>
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li>
                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                                <?= $translator->get('button_new_session') ?>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" id="switchSession">
                                <?= $translator->get('button_switch_session') ?>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#pastSessionsModal">
                                <?= $translator->get('button_past_sessions') ?>
                            </button>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <button class="btn nav-link" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <?= $translator->get('modal_title_settings') ?>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="btn nav-link" id="logoutButton">
                        <?= $translator->get('button_logout') ?>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Floating Action Button for Mobile -->
<button class="floating-add-btn" 
        data-bs-toggle="modal" 
        data-bs-target="#addRunModal" 
        <?php echo !$activeSession ? 'disabled' : ''; ?>
        title="<?= $translator->get('button_add_run') ?>">
    +
</button>
