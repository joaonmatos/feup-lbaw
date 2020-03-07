<?php
function Card($user_name, $title, $description, $posted_time, $last_updated, $url_source, $comments, $tags, $points)
{ ?>
    <!-- Card begin -->
    <div class="card mb-3" style="max-width: 800px;">
        <div class="row no-gutters">
            <div class="col-md-4">
                <!-- Image column -->
                <img src="https://picsum.photos/500" class="card-img" alt="Lorem Picsum" style="height: 100%;">
            </div>
            <div class="col-md-8">
                <div class="card-body row no-gutters">
                    <div class="col-sm-11">
                        <div class="d-flex flex-column">
                            <div class="container">
                                <small class="text-muted">
                                    <p class="text-right"><a href="#" class="card-link"><i class="fas fa-user"></i>
                                            <?= $user_name ?></a> &emsp;<i class="fas fa-clock"></i> <?= $posted_time ?> ago</p>
                                </small>
                            </div>
                            <!-- Main content column -->
                            <div class="container">
                                <h5 class="card-title"><?= $title ?></h5>
                                <p class="card-text"><?= $description ?></p>
                                <p class="card-text"><small class="text-muted">Last updated <?= $last_updated ?> ago</small></p>
                                <small class="text-muted">
                                    <a href="#" class="card-link"><i class="fas fa-external-link-alt"></i>
                                        <?= $url_source ?></a>
                                    <a href="#" class="card-link ml-2"><i class="fas fa-comments"></i> <?= $comments ?> comments</a>
                                </small>
                                <div class="absolute-bottom">
                                    <small class="text-muted">
                                        <?php for ($i = 0; $i < count($tags); $i++) :
                                            if ($i == 0) : ?>
                                                <a href="#" class="card-link"><?= $tags[$i] ?></a>
                                            <?php else : ?>
                                                <a href="#" class="card-link ml-2"><?= $tags[$i] ?></a>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col" style="padding: 0; margin: 0;">
                        <!-- Votes interaction column -->
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-arrow-up"></i>
                            <p><?= $points ?></p>
                            <i class="fas fa-arrow-down position-relative" style="bottom: 15px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>