import 'package:flutter_test/flutter_test.dart';
import 'package:ashickey/models/post.dart';
import 'package:ashickey/models/category.dart';

void main() {
  group('Data Model Tests', () {
    test('Post model parses JSON correctly', () {
      final json = {
        '_id': '12345',
        'title': 'Test Post',
        'slug': 'test-post',
        'excerpt': 'This is a test.',
        'content': 'Test content body.',
        'coverImage': 'https://example.com/img.jpg',
        'category': {
          '_id': 'cat1',
          'name': 'Technology',
          'slug': 'technology',
          'description': 'Tech stuff'
        },
        'tags': [
          {'_id': 'tag1', 'name': 'Flutter', 'slug': 'flutter'}
        ],
        'status': 'published',
        'publishedAt': '2026-04-07T00:00:00Z',
        'author': 'Test Author',
        'readTime': 5,
        'views': 100,
        'createdAt': '2026-04-01T00:00:00Z'
      };

      final post = Post.fromJson(json);

      expect(post.id, '12345');
      expect(post.title, 'Test Post');
      expect(post.slug, 'test-post');
      expect(post.readTime, 5);
      expect(post.views, 100);
      expect(post.category?.name, 'Technology');
      expect(post.tags.first.name, 'Flutter');
      expect(post.status, 'published');
      expect(post.publishedAt?.toIso8601String(), '2026-04-07T00:00:00.000Z');
    });

    test('Category model parses JSON correctly', () {
      final json = {
        '_id': 'cat1',
        'name': 'Design',
        'slug': 'design',
        'description': 'UI/UX Design',
        'postCount': 10,
        'createdAt': '2026-04-01T00:00:00Z'
      };

      final category = Category.fromJson(json);

      expect(category.id, 'cat1');
      expect(category.name, 'Design');
      expect(category.slug, 'design');
      expect(category.postCount, 10);
    });
  });
}
