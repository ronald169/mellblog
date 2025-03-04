<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
    use App\Models\Comment;

    class CommentSeeder extends Seeder
    {
        /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nbrPosts = 9;
        $nbrUsers = 3;

        foreach (range(1, $nbrPosts - 1) as $id) {
            $this->createComment($id, rand(1, $nbrUsers));
        }

        $comment = $this->createComment(2, 3);
		$this->createComment(2, 2, $comment->id);

		$comment = $this->createComment(2, 2);
		$this->createComment(2, 3, $comment->id);

		$comment = $this->createComment(2, 3, $comment->id);

		$comment = $this->createComment(2, 1, $comment->id);
		$this->createComment(2, 3, $comment->id);

		$comment = $this->createComment(4, 1);

		$comment = $this->createComment(4, 3, $comment->id);
		$this->createComment(4, 2, $comment->id);
		$this->createComment(4, 1, $comment->id);
    }

    protected function createComment($postId, $userId, $id = null)
    {
        return Comment::factory()->create([
            'post_id' => $postId,
            'user_id' => $userId,
            'parent_id' => $id,
        ]);
    }
}
