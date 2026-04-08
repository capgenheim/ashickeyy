class Post {
  final String id;
  final String title;
  final String slug;
  final String content;
  final String excerpt;
  final String coverImage;
  final CategoryRef? category;
  final List<TagRef> tags;
  final String status;
  final DateTime? publishedAt;
  final String author;
  final int readTime;
  final int views;
  final DateTime createdAt;

  Post({
    required this.id,
    required this.title,
    required this.slug,
    this.content = '',
    required this.excerpt,
    this.coverImage = '',
    this.category,
    this.tags = const [],
    required this.status,
    this.publishedAt,
    required this.author,
    required this.readTime,
    this.views = 0,
    required this.createdAt,
  });

  factory Post.fromJson(Map<String, dynamic> json) {
    return Post(
      id: json['_id'] ?? '',
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      content: json['content'] ?? '',
      excerpt: json['excerpt'] ?? '',
      coverImage: json['coverImage'] ?? '',
      category: json['category'] != null
          ? CategoryRef.fromJson(json['category'] is String
              ? {'_id': json['category'], 'name': '', 'slug': ''}
              : json['category'])
          : null,
      tags: (json['tags'] as List<dynamic>?)
              ?.map((t) => TagRef.fromJson(t is String ? {'_id': t, 'name': '', 'slug': ''} : t))
              .toList() ??
          [],
      status: json['status'] ?? 'draft',
      publishedAt: json['publishedAt'] != null ? DateTime.parse(json['publishedAt']) : null,
      author: json['author'] ?? '',
      readTime: json['readTime'] ?? 1,
      views: json['views'] ?? 0,
      createdAt: DateTime.parse(json['createdAt'] ?? DateTime.now().toIso8601String()),
    );
  }
}

class CategoryRef {
  final String id;
  final String name;
  final String slug;

  CategoryRef({required this.id, required this.name, required this.slug});

  factory CategoryRef.fromJson(Map<String, dynamic> json) {
    return CategoryRef(
      id: json['_id'] ?? '',
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
    );
  }
}

class TagRef {
  final String id;
  final String name;
  final String slug;

  TagRef({required this.id, required this.name, required this.slug});

  factory TagRef.fromJson(Map<String, dynamic> json) {
    return TagRef(
      id: json['_id'] ?? '',
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
    );
  }
}
