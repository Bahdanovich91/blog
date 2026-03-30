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

    $stmtPost = $pdo->prepare("
        INSERT INTO posts (title, slug, description, content, view_count, created_at)
        VALUES (:title, :slug, :description, :content, :views, NOW())
    ");

    $stmtRelation = $pdo->prepare("
        INSERT INTO post_category (post_id, category_id)
        VALUES (:post_id, :category_id)
    ");

    for ($i = 1; $i <= 20; $i++) {
        $stmtPost->execute([
            'title' => "Post $i",
            'slug' => "post-$i",
            'description' => $lorem,
            'content' => $lorem . ' ' . $lorem,
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
