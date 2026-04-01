<?php

declare(strict_types=1);

use App\Core\Database\Database;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Seeding started...\n";

try {
    $categories = [
        ['Category 1', 'category-1'],
        ['Category 2', 'category-2'],
        ['Category 3', 'category-3'],
        ['Category 4', 'category-4'],
    ];

    $categoryIds = [];

    $stmtCategory = $pdo->prepare(
        "INSERT INTO categories (name, slug) VALUES (:name, :slug)"
    );

    foreach ($categories as [$name, $slug]) {
        $stmtCategory->execute([
            'name' => $name,
            'slug' => $slug,
        ]);

        $categoryIds[] = (int)$pdo->lastInsertId();
    }

    echo "Categories created\n";

    // Posts
    $lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
    $images = [
        'https://spaindmsmedia.newmindmedia.com/wsimgs/D847594BB89CF67E5669354512E9B33E7660A6DD.jpg',
        'https://blog.wikium.ru/wp-content/uploads/2021/12/aiony-haust-3TLl_97HNJo-unsplash-240x300.jpg',
        'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRac52fjA7FiKft4y6CbnJ-i7weObBALJ0I1y7zTGtUDERAaxLK',
    ];

    $stmtPost = $pdo->prepare("
        INSERT INTO posts (title, slug, description, content, image, view_count, created_at)
        VALUES (:title, :slug, :description, :content, :image, :views, NOW())
    ");

    $stmtRelation = $pdo->prepare("
        INSERT INTO post_category (post_id, category_id)
        VALUES (:post_id, :category_id)
    ");

    for ($i = 1; $i <= 40; $i++) {
        $stmtPost->execute([
            'title' => "Post $i",
            'slug' => "post-$i",
            'description' => $lorem,
            'content' => $lorem . ' ' . $lorem,
            'image' => $images[array_rand($images)],
            'views' => random_int(0, 1000),
        ]);

        $postId = (int)$pdo->lastInsertId();
        $categoryId = $categoryIds[array_rand($categoryIds)];

        $stmtRelation->execute([
            'post_id' => $postId,
            'category_id' => $categoryId,
        ]);
    }

    echo "Completed successfully";

} catch (\Throwable $e) {
    echo $e->getMessage();
    exit(1);
}
