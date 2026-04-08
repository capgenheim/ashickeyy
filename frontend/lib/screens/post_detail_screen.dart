import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../services/api_service.dart';
import '../models/post.dart';
import '../widgets/markdown_view.dart';
import '../widgets/footer.dart';
import '../widgets/tag_chip.dart';
import '../utils/date_formatter.dart';
import '../utils/responsive.dart';

class PostDetailScreen extends StatefulWidget {
  final String slug;

  const PostDetailScreen({super.key, required this.slug});

  @override
  State<PostDetailScreen> createState() => _PostDetailScreenState();
}

class _PostDetailScreenState extends State<PostDetailScreen> {
  final ApiService _api = ApiService();
  Post? _post;
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchPost();
  }

  Future<void> _fetchPost() async {
    try {
      final post = await _api.getPost(widget.slug);
      if (mounted) setState(() { _post = post; _loading = false; });
    } catch (e) {
      if (mounted) setState(() { _error = 'Post not found'; _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    final isDesktop = Responsive.isDesktop(context);

    if (_loading) {
      return const Center(child: CircularProgressIndicator(strokeWidth: 2));
    }

    if (_error != null || _post == null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.error_outline, size: 48, color: colorScheme.onSurfaceVariant.withValues(alpha: 0.4)),
            const SizedBox(height: 16),
            Text(_error ?? 'Post not found', style: textTheme.titleMedium),
          ],
        ),
      );
    }

    final post = _post!;

    return SingleChildScrollView(
      child: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 680),
          child: Padding(
            padding: EdgeInsets.symmetric(
              horizontal: isDesktop ? 0 : 24,
              vertical: 40,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Category
                if (post.category != null)
                  Text(
                    post.category!.name,
                    style: textTheme.bodyMedium?.copyWith(
                      color: colorScheme.primary,
                      fontWeight: FontWeight.w600,
                    ),
                  ).animate().fadeIn(duration: 300.ms),
                if (post.category != null) const SizedBox(height: 16),

                // Title
                Text(
                  post.title,
                  style: textTheme.displaySmall?.copyWith(
                    fontWeight: FontWeight.w800,
                    height: 1.2,
                    letterSpacing: -0.5,
                  ),
                ).animate().fadeIn(duration: 400.ms, delay: 100.ms),
                const SizedBox(height: 16),

                // Excerpt as subtitle
                Text(
                  post.excerpt,
                  style: textTheme.titleMedium?.copyWith(
                    color: colorScheme.onSurfaceVariant,
                    height: 1.5,
                    fontWeight: FontWeight.w400,
                  ),
                ).animate().fadeIn(duration: 400.ms, delay: 200.ms),
                const SizedBox(height: 24),

                // Author row
                Row(
                  children: [
                    Container(
                      width: 50,
                      height: 50,
                      decoration: const BoxDecoration(
                        shape: BoxShape.circle,
                        image: DecorationImage(
                          image: AssetImage('assets/images/acap.png'),
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'acap',
                          style: textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w600),
                        ),
                        Row(
                          children: [
                            Text(
                              DateFormatter.format(post.publishedAt),
                              style: textTheme.bodySmall?.copyWith(color: colorScheme.onSurfaceVariant),
                            ),
                            Text(
                              '  ·  ${post.readTime} min read',
                              style: textTheme.bodySmall?.copyWith(color: colorScheme.onSurfaceVariant),
                            ),
                            if (post.views > 0)
                              Text(
                                '  ·  ${post.views} views',
                                style: textTheme.bodySmall?.copyWith(color: colorScheme.onSurfaceVariant),
                              ),
                          ],
                        ),
                      ],
                    ),
                  ],
                ).animate().fadeIn(duration: 400.ms, delay: 300.ms),

                const SizedBox(height: 28),
                Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
                const SizedBox(height: 28),

                // Cover image
                if (post.coverImage.isNotEmpty) ...[
                  ClipRRect(
                    borderRadius: BorderRadius.circular(4),
                    child: AspectRatio(
                      aspectRatio: 16 / 9,
                      child: Image.network(
                        post.coverImage,
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => Container(
                          color: colorScheme.surfaceContainerHighest,
                        ),
                      ),
                    ),
                  ).animate().fadeIn(duration: 500.ms, delay: 400.ms),
                  const SizedBox(height: 32),
                ],

                // Content
                MarkdownView(data: post.content)
                    .animate()
                    .fadeIn(duration: 500.ms, delay: 400.ms),

                const SizedBox(height: 48),

                // Tags
                if (post.tags.isNotEmpty) ...[
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: post.tags.map((tag) => Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                      decoration: BoxDecoration(
                        color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Text(
                        tag.name,
                        style: textTheme.bodySmall?.copyWith(
                          color: colorScheme.onSurfaceVariant,
                        ),
                      ),
                    )).toList(),
                  ),
                  const SizedBox(height: 40),
                ],

                Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
                const SizedBox(height: 40),
                const AppFooter(),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
