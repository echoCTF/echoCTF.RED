<div class="card card-stats mb-4 mb-xl-0">
  <div class="card-body">
    <div class="row">
      <div class="col">
        <h5 class="card-title text-uppercase text-muted mb-0"><?= $title ?></h5>
        <span class="h2 font-weight-bold mb-0"><?= $total ?></span>
      </div>
      <div class="col-auto">
        <div class="icon icon-shape bg-<?= $color ?> text-white rounded-circle shadow">
          <i class="<?= $icon ?>"></i>
        </div>
      </div>
    </div>
    <p class="mt-3 mb-0 text-muted text-sm">
      <abbr title="Yesterday <?= $yesterday ?>">
        <?php if ($today > $yesterday) : ?>
          <span class="text-success mr-2"><i class="fa fa-arrow-up"></i>
        <?php elseif ($today == $yesterday) : ?>
          <span class="text-info mr-2"><i class="fa fa-equals"></i>
        <?php else : ?>
          <span class="text-danger mr-2"><i class="fa fa-arrow-down"></i>
        <?php endif; ?>
            <?= number_format($today) ?>
          </span>
        <span class="text-nowrap">Today</span>
      </abbr>
    </p>
  </div>
</div>