<?php

namespace Database\Seeders;

use App\Models\DsaPost;
use Illuminate\Database\Seeder;

class DsaPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Introduction to Data Structures and Algorithms',
                'slug' => 'introduction-to-dsa',
                'content' => '<h2>What are Data Structures?</h2><p>Data structures are ways to organize and store data in a computer so that it can be used efficiently. Common examples include arrays, linked lists, stacks, queues, trees, and graphs.</p><h2>Why Learn DSA?</h2><p>Mastering DSA is essential for cracking technical interviews at top tech companies. It helps you write efficient code and solve complex problems.</p>',
                'excerpt' => 'A beginner-friendly introduction to data structures and algorithms and why they matter.',
                'status' => 'active',
                'order' => 1,
            ],
            [
                'title' => 'Arrays: The Foundation of Programming',
                'slug' => 'arrays-foundation-programming',
                'content' => '<h2>Understanding Arrays</h2><p>An array is a collection of elements stored at contiguous memory locations. It is one of the simplest and most used data structures.</p><h2>Key Operations</h2><ul><li>Access: O(1)</li><li>Search: O(n)</li><li>Insertion: O(n)</li><li>Deletion: O(n)</li></ul>',
                'excerpt' => 'Learn the basics of arrays and their time complexities.',
                'status' => 'active',
                'order' => 2,
            ],
            [
                'title' => 'Linked List vs Array: When to Use Which?',
                'slug' => 'linked-list-vs-array',
                'content' => '<h2>Linked List</h2><p>A linked list is a linear data structure where elements are not stored at contiguous locations. Each element points to the next.</p><h2>Comparison</h2><p>Arrays are better for random access, while linked lists excel at insertions and deletions. Choose based on your use case.</p>',
                'excerpt' => 'Compare linked lists and arrays to make better design decisions.',
                'status' => 'active',
                'order' => 3,
            ],
            [
                'title' => 'Binary Search: Divide and Conquer',
                'slug' => 'binary-search-divide-conquer',
                'content' => '<h2>The Algorithm</h2><p>Binary search works on sorted arrays by repeatedly dividing the search interval in half. If the value is less than the middle element, search the left half; otherwise search the right half.</p><h2>Time Complexity</h2><p>Binary search has O(log n) time complexity, making it extremely efficient for large datasets.</p>',
                'excerpt' => 'Master the binary search algorithm and its applications.',
                'status' => 'active',
                'order' => 4,
            ],
            [
                'title' => 'Sorting Algorithms: Quick Sort Explained',
                'slug' => 'quick-sort-explained',
                'content' => '<h2>How Quick Sort Works</h2><p>Quick Sort picks a pivot element and partitions the array around it. Elements smaller than pivot go left, larger go right. Then recursively sort the sub-arrays.</p><h2>Performance</h2><p>Average case: O(n log n), Worst case: O(n²). One of the most used sorting algorithms in practice.</p>',
                'excerpt' => 'Deep dive into Quick Sort algorithm with examples.',
                'status' => 'active',
                'order' => 5,
            ],
        ];

        foreach ($posts as $post) {
            DsaPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post
            );
        }
    }
}
