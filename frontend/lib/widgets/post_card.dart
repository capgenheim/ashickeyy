import 'package:flutter/material.dart';
import '../models/post.dart';
import '../utils/date_formatter.dart';
import 'post_reader_modal.dart';

class PostCard extends StatefulWidget {
  final Post post;
  final bool isFeatured;

  const PostCard({super.key, required this.post, this.isFeatured = false});

  @override
  State<PostCard> createState() => _PostCardState();
}

class _PostCardState extends State<PostCard> {
  bool _isHovered = false;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    return MouseRegion(
      onEnter: (_) => setState(() => _isHovered = true),
      onExit: (_) => setState(() => _isHovered = false),
      cursor: SystemMouseCursors.click,
      child: GestureDetector(
        onTap: () => PostReaderModal.show(context, widget.post.slug),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Author row
              Row(
                children: [
                  // Author avatar
                  Container(
                    width: 24,
                    height: 24,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [colorScheme.primary, colorScheme.tertiary],
                      ),
                      shape: BoxShape.circle,
                    ),
                    child: Center(
                      child: Text(
                        'A',
                        style: TextStyle(
                          color: colorScheme.onPrimary,
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'ashickey{}',
                    style: textTheme.bodySmall?.copyWith(
                      fontWeight: FontWeight.w600,
                      color: colorScheme.onSurface,
                    ),
                  ),
                  if (widget.post.category != null) ...[
                    Text(
                      '  in  ',
                      style: textTheme.bodySmall?.copyWith(
                        color: colorScheme.onSurfaceVariant,
                      ),
                    ),
                    Text(
                      widget.post.category!.name,
                      style: textTheme.bodySmall?.copyWith(
                        fontWeight: FontWeight.w600,
                        color: colorScheme.onSurface,
                      ),
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 12),

              // Main content row
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Text content
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Title
                        AnimatedDefaultTextStyle(
                          duration: const Duration(milliseconds: 150),
                          style: (widget.isFeatured
                                  ? textTheme.headlineSmall
                                  : textTheme.titleMedium)
                              ?.copyWith(
                            fontWeight: FontWeight.w800,
                            height: 1.25,
                            letterSpacing: -0.3,
                            color: _isHovered
                                ? colorScheme.onSurface.withValues(alpha: 0.7)
                                : colorScheme.onSurface,
                          ) ?? const TextStyle(),
                          child: Text(
                            widget.post.title,
                            maxLines: widget.isFeatured ? 3 : 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        const SizedBox(height: 6),

                        // Excerpt
                        Text(
                          widget.post.excerpt,
                          style: textTheme.bodyMedium?.copyWith(
                            color: colorScheme.onSurfaceVariant,
                            height: 1.5,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 12),

                        // Meta row
                        Row(
                          children: [
                            Text(
                              DateFormatter.relative(widget.post.publishedAt),
                              style: textTheme.bodySmall?.copyWith(
                                color: colorScheme.onSurfaceVariant.withValues(alpha: 0.8),
                                fontSize: 13,
                              ),
                            ),
                            _MetaDot(color: colorScheme.onSurfaceVariant),
                            Text(
                              '${widget.post.readTime} min read',
                              style: textTheme.bodySmall?.copyWith(
                                color: colorScheme.onSurfaceVariant.withValues(alpha: 0.8),
                                fontSize: 13,
                              ),
                            ),
                            if (widget.post.views > 0) ...[
                              _MetaDot(color: colorScheme.onSurfaceVariant),
                              Icon(
                                Icons.bar_chart_rounded,
                                size: 14,
                                color: colorScheme.onSurfaceVariant.withValues(alpha: 0.6),
                              ),
                              const SizedBox(width: 3),
                              Text(
                                '${widget.post.views}',
                                style: textTheme.bodySmall?.copyWith(
                                  color: colorScheme.onSurfaceVariant.withValues(alpha: 0.8),
                                  fontSize: 13,
                                ),
                              ),
                            ],
                          ],
                        ),
                      ],
                    ),
                  ),

                  // Thumbnail placeholder (right side, Medium-style)
                  if (widget.post.coverImage.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(left: 24),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: SizedBox(
                          width: widget.isFeatured ? 160 : 120,
                          height: widget.isFeatured ? 120 : 80,
                          child: Image.network(
                            widget.post.coverImage,
                            fit: BoxFit.cover,
                            errorBuilder: (_, __, ___) => Container(
                              color: colorScheme.surfaceContainerHighest,
                            ),
                          ),
                        ),
                      ),
                    )
                  else
                    Padding(
                      padding: const EdgeInsets.only(left: 24),
                      child: Container(
                        width: widget.isFeatured ? 160 : 120,
                        height: widget.isFeatured ? 120 : 80,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(4),
                          gradient: LinearGradient(
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                            colors: [
                              colorScheme.primaryContainer.withValues(alpha: 0.6),
                              colorScheme.secondaryContainer.withValues(alpha: 0.4),
                            ],
                          ),
                        ),
                        child: Center(
                          child: Icon(
                            Icons.article_outlined,
                            color: colorScheme.primary.withValues(alpha: 0.5),
                            size: widget.isFeatured ? 36 : 28,
                          ),
                        ),
                      ),
                    ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MetaDot extends StatelessWidget {
  final Color color;
  const _MetaDot({required this.color});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 6),
      child: Text(
        '·',
        style: TextStyle(
          color: color,
          fontWeight: FontWeight.w900,
          fontSize: 14,
        ),
      ),
    );
  }
}
