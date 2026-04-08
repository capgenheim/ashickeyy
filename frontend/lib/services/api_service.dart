import 'package:dio/dio.dart';
import '../config/constants.dart';
import '../models/post.dart';
import '../models/category.dart';
import '../models/tag.dart';

class PostsResponse {
  final List<Post> posts;
  final String? nextCursor;
  final bool hasMore;

  PostsResponse({required this.posts, this.nextCursor, required this.hasMore});
}

class ApiService {
  late final Dio _dio;

  // In-memory cache
  final Map<String, _CacheEntry> _cache = {};
  static const Duration _cacheDuration = Duration(seconds: 60);

  ApiService() {
    _dio = Dio(BaseOptions(
      baseUrl: AppConstants.apiBaseUrl,
      connectTimeout: const Duration(seconds: 10),
      receiveTimeout: const Duration(seconds: 10),
      headers: {
        'Accept': 'application/json',
      },
    ));
  }

  T? _getFromCache<T>(String key) {
    final entry = _cache[key];
    if (entry != null && DateTime.now().isBefore(entry.expiresAt)) {
      return entry.data as T;
    }
    _cache.remove(key);
    return null;
  }

  void _setCache(String key, dynamic data) {
    _cache[key] = _CacheEntry(data: data, expiresAt: DateTime.now().add(_cacheDuration));
  }

  Future<PostsResponse> getPosts({
    String? cursor,
    int limit = AppConstants.postsPerPage,
    String? category,
    String? tag,
    String? search,
  }) async {
    final params = <String, dynamic>{'limit': limit};
    if (cursor != null) params['cursor'] = cursor;
    if (category != null) params['category'] = category;
    if (tag != null) params['tag'] = tag;
    if (search != null) params['q'] = search;

    final cacheKey = 'posts:$params';
    final cached = _getFromCache<PostsResponse>(cacheKey);
    if (cached != null) return cached;

    final response = await _dio.get('/posts', queryParameters: params);
    final data = response.data;

    final result = PostsResponse(
      posts: (data['posts'] as List).map((p) => Post.fromJson(p)).toList(),
      nextCursor: data['nextCursor'],
      hasMore: data['hasMore'] ?? false,
    );

    _setCache(cacheKey, result);
    return result;
  }

  Future<Post> getPost(String slug) async {
    final cacheKey = 'post:$slug';
    final cached = _getFromCache<Post>(cacheKey);
    if (cached != null) return cached;

    final response = await _dio.get('/posts/$slug');
    final post = Post.fromJson(response.data['post']);

    _setCache(cacheKey, post);
    return post;
  }

  Future<List<Category>> getCategories() async {
    const cacheKey = 'categories';
    final cached = _getFromCache<List<Category>>(cacheKey);
    if (cached != null) return cached;

    final response = await _dio.get('/categories');
    final categories = (response.data['categories'] as List)
        .map((c) => Category.fromJson(c))
        .toList();

    _setCache(cacheKey, categories);
    return categories;
  }

  Future<List<Tag>> getTags() async {
    const cacheKey = 'tags';
    final cached = _getFromCache<List<Tag>>(cacheKey);
    if (cached != null) return cached;

    final response = await _dio.get('/tags');
    final tags = (response.data['tags'] as List)
        .map((t) => Tag.fromJson(t))
        .toList();

    _setCache(cacheKey, tags);
    return tags;
  }

  void clearCache() {
    _cache.clear();
  }
}

class _CacheEntry {
  final dynamic data;
  final DateTime expiresAt;

  _CacheEntry({required this.data, required this.expiresAt});
}
