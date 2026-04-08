import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:flutter_markdown/flutter_markdown.dart';
import 'package:share_plus/share_plus.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'dart:html' as html;
import '../models/post.dart';
import '../services/api_service.dart';
import '../utils/date_formatter.dart';

class PostReaderModal extends StatefulWidget {
  final String slug;

  const PostReaderModal({super.key, required this.slug});

  static void show(BuildContext context, String slug) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => PostReaderModal(slug: slug),
    );
  }

  @override
  State<PostReaderModal> createState() => _PostReaderModalState();
}

class _PostReaderModalState extends State<PostReaderModal> {
  final ApiService _api = ApiService();
  Post? _post;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _fetchPost();
  }

  Future<void> _fetchPost() async {
    try {
      final post = await _api.getPost(widget.slug);
      if (mounted) {
        setState(() {
          _post = post;
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _sharePost() {
    if (_post == null) return;
    final url = '${html.window.location.origin}/post/${_post!.slug}';
    final shareText = 'Read "${_post!.title}" on ashickey{}\n\n$url';
    Share.share(shareText, subject: _post!.title).catchError((_) {
      Clipboard.setData(ClipboardData(text: url));
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Link copied to clipboard!')),
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    return DraggableScrollableSheet(
      initialChildSize: 0.93,
      minChildSize: 0.5,
      maxChildSize: 0.97,
      builder: (_, controller) {
        return Container(
          decoration: BoxDecoration(
            color: colorScheme.surface,
            borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
          ),
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _post == null
                  ? const Center(child: Text('Failed to load post'))
                  : Column(
                      children: [
                        // Drag handle + close
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          child: Row(
                            children: [
                              const Spacer(),
                              Container(
                                width: 36,
                                height: 4,
                                decoration: BoxDecoration(
                                  color: colorScheme.onSurfaceVariant.withValues(alpha: 0.3),
                                  borderRadius: BorderRadius.circular(2),
                                ),
                              ),
                              const Spacer(),
                              IconButton(
                                tooltip: 'Expand article fullscreen',
                                icon: Icon(Icons.open_in_full_rounded, size: 20, color: colorScheme.onSurfaceVariant),
                                onPressed: () {
                                  Navigator.pop(context);
                                  context.push('/post/${widget.slug}');
                                },
                              ),
                              IconButton(
                                icon: Icon(Icons.close, size: 20, color: colorScheme.onSurfaceVariant),
                                onPressed: () => Navigator.pop(context),
                              ),
                            ],
                          ),
                        ),
                        Expanded(
                          child: SingleChildScrollView(
                            controller: controller,
                            child: Center(
                              child: ConstrainedBox(
                                constraints: const BoxConstraints(maxWidth: 680),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      // Title
                                      Text(
                                        _post!.title,
                                        style: textTheme.displaySmall?.copyWith(
                                          fontWeight: FontWeight.w800,
                                          height: 1.2,
                                          letterSpacing: -0.5,
                                        ),
                                      ).animate().fadeIn(duration: 400.ms),
                                      const SizedBox(height: 12),

                                      // Subtitle (excerpt)
                                      Text(
                                        _post!.excerpt,
                                        style: textTheme.titleMedium?.copyWith(
                                          color: colorScheme.onSurfaceVariant,
                                          height: 1.5,
                                          fontWeight: FontWeight.w400,
                                        ),
                                      ).animate(delay: 100.ms).fadeIn(duration: 400.ms),
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
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Text(
                                                  'acap',
                                                  style: textTheme.bodyMedium?.copyWith(
                                                    fontWeight: FontWeight.w600,
                                                  ),
                                                ),
                                                Text(
                                                  '${DateFormatter.relative(_post!.publishedAt)} · ${_post!.readTime} min read',
                                                  style: textTheme.bodySmall?.copyWith(
                                                    color: colorScheme.onSurfaceVariant,
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                          IconButton(
                                            icon: Icon(Icons.share_outlined, size: 20, color: colorScheme.onSurfaceVariant),
                                            onPressed: _sharePost,
                                            tooltip: 'Share',
                                          ),
                                        ],
                                      ).animate(delay: 200.ms).fadeIn(duration: 400.ms),

                                      const SizedBox(height: 24),
                                      Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
                                      const SizedBox(height: 24),

                                      // Cover image
                                      if (_post!.coverImage.isNotEmpty) ...[
                                        ClipRRect(
                                          borderRadius: BorderRadius.circular(4),
                                          child: Image.network(_post!.coverImage, fit: BoxFit.cover),
                                        ),
                                        const SizedBox(height: 32),
                                      ],

                                      // Content
                                      MarkdownBody(
                                        data: _post!.content ?? '',
                                        selectable: true,
                                        styleSheet: MarkdownStyleSheet.fromTheme(Theme.of(context)).copyWith(
                                          p: textTheme.bodyLarge?.copyWith(
                                            height: 1.9,
                                            fontSize: 18,
                                          ),
                                          h1: textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.w700),
                                          h2: textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w700),
                                          h3: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w600),
                                          blockquotePadding: const EdgeInsets.only(left: 16, top: 8, bottom: 8),
                                          blockquoteDecoration: BoxDecoration(
                                            border: Border(
                                              left: BorderSide(
                                                color: colorScheme.onSurfaceVariant.withValues(alpha: 0.3),
                                                width: 3,
                                              ),
                                            ),
                                          ),
                                        ),
                                      ).animate(delay: 300.ms).fadeIn(duration: 500.ms),

                                      const SizedBox(height: 48),

                                      // Tags
                                      if (_post!.tags.isNotEmpty) ...[
                                        Wrap(
                                          spacing: 8,
                                          runSpacing: 8,
                                          children: _post!.tags.map((tag) => Container(
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
                                        const SizedBox(height: 32),
                                      ],

                                      // Divider
                                      Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
                                      const SizedBox(height: 24),

                                      // Bottom share CTA
                                      Center(
                                        child: Column(
                                          children: [
                                            Icon(
                                              Icons.favorite_outline_rounded,
                                              size: 28,
                                              color: colorScheme.onSurfaceVariant.withValues(alpha: 0.5),
                                            ),
                                            const SizedBox(height: 12),
                                            Text(
                                              'Enjoyed this story?',
                                              style: textTheme.titleMedium?.copyWith(
                                                fontWeight: FontWeight.w600,
                                              ),
                                            ),
                                            const SizedBox(height: 4),
                                            Text(
                                              'Share it with others who might find it useful.',
                                              style: textTheme.bodySmall?.copyWith(
                                                color: colorScheme.onSurfaceVariant,
                                              ),
                                            ),
                                            const SizedBox(height: 16),
                                            OutlinedButton.icon(
                                              onPressed: _sharePost,
                                              icon: const Icon(Icons.share_outlined, size: 16),
                                              label: const Text('Share this story'),
                                              style: OutlinedButton.styleFrom(
                                                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
                                                side: BorderSide(color: colorScheme.outlineVariant),
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                      const SizedBox(height: 80),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
        );
      },
    );
  }
}
