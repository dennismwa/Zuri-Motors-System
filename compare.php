<?php
/**
 * Zuri Motors - Car Comparison Page
 */
require_once __DIR__ . '/functions.php';

$companyName = Settings::get('company_name', 'Zuri Motors');
$compareCars = getCompareCars();
$maxCompare = (int)Settings::get('max_compare_cars', 4);

$pageSeo = seoMeta('Compare Cars - ' . $companyName, 'Compare vehicles side by side to find the best car for your needs.');
include __DIR__ . '/header.php';
?>

<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4">
        <?= breadcrumbs([['label' => 'Home', 'url' => BASE_URL . '/'], ['label' => 'Compare Cars']]) ?>
    </div>
</div>

<section class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-dark-900 font-display">Compare Cars</h1>
                <p class="text-sm text-gray-500 mt-1"><?= count($compareCars) ?> of <?= $maxCompare ?> vehicles selected</p>
            </div>
            <?php if (!empty($compareCars)): ?>
            <a href="<?= BASE_URL ?>/api/compare?action=clear&csrf_token=<?= CSRF::generate() ?>" class="btn btn-outline btn-sm text-red-500 border-red-200 hover:bg-red-50 hover:border-red-300">
                <i data-lucide="trash-2" class="w-4 h-4"></i> Clear All
            </a>
            <?php endif; ?>
        </div>

        <?php if (empty($compareCars)): ?>
            <div class="bg-white rounded-2xl border border-gray-200 p-12">
                <?= renderEmptyState('No cars to compare', 'Browse our listings and add cars to compare them side by side.', 'bar-chart-2') ?>
                <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>/cars" class="btn btn-primary"><i data-lucide="car" class="w-4 h-4"></i> Browse Cars</a>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
                <table class="compare-table w-full">
                    <!-- Car Images & Names -->
                    <thead>
                        <tr>
                            <th class="bg-gray-50 w-44">Vehicle</th>
                            <?php foreach ($compareCars as $car): 
                                $img = $car['primary_image'] ? BASE_URL . '/' . ltrim($car['primary_image'], '/') : BASE_URL . '/assets/images/car-placeholder.jpg';
                            ?>
                            <td class="text-center min-w-[220px] p-4">
                                <div class="relative inline-block mb-3">
                                    <a href="<?= BASE_URL ?>/car/<?= $car['slug'] ?>">
                                        <img src="<?= $img ?>" alt="<?= clean($car['title']) ?>" class="w-full h-36 object-cover rounded-xl" loading="lazy">
                                    </a>
                                    <a href="<?= BASE_URL ?>/api/compare?action=remove&car_id=<?= $car['id'] ?>&csrf_token=<?= CSRF::generate() ?>" 
                                       class="absolute -top-2 -right-2 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow transition" title="Remove">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                                <h3 class="font-bold text-dark-900 text-sm mb-1">
                                    <a href="<?= BASE_URL ?>/car/<?= $car['slug'] ?>" class="hover:text-primary-600 transition"><?= clean($car['title']) ?></a>
                                </h3>
                                <p class="text-lg font-bold text-primary-600"><?= formatPrice($car['price']) ?></p>
                                <?php if ($car['old_price']): ?>
                                <p class="text-xs text-gray-400 line-through"><?= formatPrice($car['old_price']) ?></p>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $specs = [
                            ['label' => 'Condition', 'key' => 'condition_type', 'format' => fn($v) => ucfirst($v)],
                            ['label' => 'Brand', 'key' => 'brand_name'],
                            ['label' => 'Model', 'key' => 'model'],
                            ['label' => 'Year', 'key' => 'year'],
                            ['label' => 'Mileage', 'key' => 'mileage', 'format' => fn($v, $c) => formatMileage($v, $c['mileage_unit'])],
                            ['label' => 'Fuel Type', 'key' => 'fuel_type', 'format' => fn($v) => ucfirst(str_replace('_',' ',$v))],
                            ['label' => 'Transmission', 'key' => 'transmission', 'format' => fn($v) => ucfirst(str_replace('_',' ',$v))],
                            ['label' => 'Engine Size', 'key' => 'engine_size'],
                            ['label' => 'Horsepower', 'key' => 'horsepower', 'format' => fn($v) => $v ? $v . ' hp' : '-'],
                            ['label' => 'Body Type', 'key' => 'category_name'],
                            ['label' => 'Color', 'key' => 'color'],
                            ['label' => 'Interior', 'key' => 'interior_color'],
                            ['label' => 'Doors', 'key' => 'doors'],
                            ['label' => 'Seats', 'key' => 'seats'],
                            ['label' => 'Drivetrain', 'key' => 'drivetrain', 'format' => fn($v) => $v ? strtoupper($v) : '-'],
                            ['label' => 'Location', 'key' => 'location'],
                        ];
                        foreach ($specs as $spec):
                        ?>
                        <tr>
                            <th><?= $spec['label'] ?></th>
                            <?php foreach ($compareCars as $car): 
                                $val = $car[$spec['key']] ?? '-';
                                if (isset($spec['format'])) {
                                    $val = $spec['format']($val, $car);
                                }
                            ?>
                            <td class="text-center font-medium"><?= clean($val ?: '-') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Features comparison -->
                        <tr>
                            <th class="bg-primary-50 text-primary-700">Features</th>
                            <?php foreach ($compareCars as $car): ?>
                            <td class="bg-primary-50"></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php
                        // Gather all features
                        $allFeatures = [];
                        $carFeatureMap = [];
                        foreach ($compareCars as $car) {
                            $cf = getCarFeatures($car['id']);
                            foreach ($cf as $f) {
                                $allFeatures[$f['feature_id']] = $f['name'];
                                $carFeatureMap[$car['id']][$f['feature_id']] = true;
                            }
                        }
                        ksort($allFeatures);
                        foreach ($allFeatures as $fid => $fname):
                        ?>
                        <tr>
                            <th class="font-normal text-gray-600 text-xs"><?= clean($fname) ?></th>
                            <?php foreach ($compareCars as $car): ?>
                            <td class="text-center">
                                <?php if (!empty($carFeatureMap[$car['id']][$fid])): ?>
                                <i data-lucide="check-circle" class="w-5 h-5 text-primary-500 inline"></i>
                                <?php else: ?>
                                <i data-lucide="minus" class="w-5 h-5 text-gray-300 inline"></i>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Action row -->
                        <tr>
                            <th>Actions</th>
                            <?php foreach ($compareCars as $car): ?>
                            <td class="text-center p-4">
                                <a href="<?= BASE_URL ?>/car/<?= $car['slug'] ?>" class="btn btn-primary btn-sm w-full mb-2">View Details</a>
                                <a href="<?= whatsappLink('Hi, I\'m interested in ' . $car['title'] . ' (' . formatPrice($car['price']) . ')') ?>" target="_blank" class="btn btn-sm w-full bg-[#25D366] text-white hover:bg-[#20BD5A]">WhatsApp</a>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if (count($compareCars) < $maxCompare): ?>
            <div class="text-center mt-6">
                <a href="<?= BASE_URL ?>/cars" class="btn btn-outline"><i data-lucide="plus" class="w-4 h-4"></i> Add More Cars to Compare</a>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
