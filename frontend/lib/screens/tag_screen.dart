import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:go_router/go_router.dart';
import '../services/api_service.dart';
import '../models/tag.dart';
import '../models/post.dart';
import '../widgets/post_card.dart';
import '../widgets/shimmer_loader.dart';
import '../widgets/footer.dart';
import '../widgets/animated_list_item.dart';
import '../utils/responsive.dart';

class TagScreen extends StatefulWidget {
  final String? selectedSlug;

  const TagScreen({super.key, this.selectedSlug});

  @override
  State<TagScreen> createState() => _TagScreenState();
}

class _TagScreenState extends State<TagScreen> {
  final ApiService _api = ApiService();
  List<Tag> _tags = [];
  List<Post> _posts = [];
  bool _loadingTags = true;
  bool _loadingPosts = false;
  String? _selectedSlug;

  @override
  void initState() {
    super.initState();
    _selectedSlug = widget.selectedSlug;
    _fetchTags();
    if (_selectedSlug != null) _fetchPosts(_selectedSlug!);
  }

  Future<void> _fetchTags() async {
    try {
      final tags = await _api.getTags();
      if (mounted) setState(() { _tags = tags; _loadingTags = false; });
    } catch (e) {
      if (mounted) setState(() => _loadingTags = false);
    }
  }

  Future<void> _fetchPosts(String tagSlug) async {
    setState(() { _loadingPosts = true; _selectedSlug = tagSlug; });
    try {
      final response = await _api.getPosts(tag: tagSlug);
      if (mounted) setState(() { _posts = response.posts; _loadingPosts = false; });
    } catch (e) {
      if (mounted) setState(() => _loadingPosts = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    final isDesktop = Responsive.isDesktop(context);

    return SingleChildScrollView(
      child: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 700),
          child: Padding(
            padding: EdgeInsets.symmetric(
              horizontal: isDesktop ? 0 : 24,
              vertical: 32,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Explore by Tags',
                  style: textTheme.headlineMedium?.copyWith(
                    fontWeight: FontWeight.w800,
                    letterSpacing: -0.5,
                  ),
                ).animate().fadeIn(duration: 400.ms),
                const SizedBox(height: 8),
                Text(
                  'Browse stories by tag.',
                  style: textTheme.bodyMedium?.copyWith(
                    color: colorScheme.onSurfaceVariant,
                  ),
                ).animate().fadeIn(duration: 400.ms, delay: 100.ms),
                const SizedBox(height: 24),

                // Tag pills
                if (_loadingTags)
                  const Center(child: CircularProgressIndicator(strokeWidth: 2))
                else
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: _tags.map((tag) {
                      final isSelected = _selectedSlug == tag.slug;
                      return _TagPill(
                        label: '#${tag.name}',
                        isSelected: isSelected,
                        onTap: () {
                          context.go('/tags/${tag.slug}');
                          _fetchPosts(tag.slug);
                        },
                        colorScheme: colorScheme,
                      );
                    }).toList(),
                  ).animate().fadeIn(duration: 400.ms, delay: 200.ms),

                const SizedBox(height: 24),
                Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),

                // Filtered posts
                if (_selectedSlug != null) ...[
                  if (_loadingPosts)
                    const Padding(
                      padding: EdgeInsets.only(top: 16),
                      child: ShimmerPostList(count: 3),
                    )
                  else if (_posts.isEmpty)
                    Center(
                      child: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 60),
                        child: Column(
                          children: [
                            Icon(
                              Icons.label_off_outlined,
                              size: 48,
                              color: colorScheme.onSurfaceVariant.withValues(alpha: 0.4),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              'No stories with this tag yet.',
                              style: textTheme.bodyLarge?.copyWith(
                                color: colorScheme.onSurfaceVariant,
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                  else
                    ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: _posts.length,
                      separatorBuilder: (context, index) => Divider(
                        color: colorScheme.outlineVariant.withValues(alpha: 0.2),
                        height: 1,
                      ),
                      itemBuilder: (context, index) {
                        return AnimatedListItem(
                          index: index,
                          child: PostCard(post: _posts[index]),
                        );
                      },
                    ),
                ] else
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 48),
                    child: Center(
                      child: Text(
                        'Select a tag above to browse stories.',
                        style: textTheme.bodyMedium?.copyWith(
                          color: colorScheme.onSurfaceVariant.withValues(alpha: 0.6),
                          fontStyle: FontStyle.italic,
                        ),
                      ),
                    ),
                  ),

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

class _TagPill extends StatefulWidget {
  final String label;
  final bool isSelected;
  final VoidCallback onTap;
  final ColorScheme colorScheme;

  const _TagPill({
    required this.label,
    required this.isSelected,
    required this.onTap,
    required this.colorScheme,
  });

  @override
  State<_TagPill> createState() => _TagPillState();
}

class _TagPillState extends State<_TagPill> {
  bool _isHovered = false;

  @override
  Widget build(BuildContext context) {
    return MouseRegion(
      cursor: SystemMouseCursors.click,
      onEnter: (_) => setState(() => _isHovered = true),
      onExit: (_) => setState(() => _isHovered = false),
      child: GestureDetector(
        onTap: widget.onTap,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
          decoration: BoxDecoration(
            color: widget.isSelected
                ? widget.colorScheme.onSurface
                : _isHovered
                    ? widget.colorScheme.surfaceContainerHighest
                    : widget.colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
            borderRadius: BorderRadius.circular(24),
          ),
          child: Text(
            widget.label,
            style: TextStyle(
              fontSize: 14,
              fontWeight: widget.isSelected ? FontWeight.w600 : FontWeight.w500,
              color: widget.isSelected
                  ? widget.colorScheme.surface
                  : widget.colorScheme.onSurface,
            ),
          ),
        ),
      ),
    );
  }
}
