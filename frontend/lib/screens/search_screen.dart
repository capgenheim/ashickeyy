import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:go_router/go_router.dart';
import '../services/api_service.dart';
import '../models/post.dart';
import '../models/tag.dart';
import '../widgets/post_card.dart';
import '../widgets/shimmer_loader.dart';
import '../widgets/footer.dart';
import '../widgets/animated_list_item.dart';
import '../utils/responsive.dart';

class SearchScreen extends StatefulWidget {
  final String? initialQuery;
  final String? initialTag;

  const SearchScreen({super.key, this.initialQuery, this.initialTag});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final ApiService _api = ApiService();
  final TextEditingController _controller = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  List<Post> _results = [];
  List<Tag> _tags = [];
  bool _loading = false;
  bool _loadingTags = true;
  bool _hasSearched = false;
  bool _showScrollToTop = false;
  String? _selectedTag;
  bool _searchByTag = false;

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
    _fetchTags();

    // Auto-search if tag passed
    if (widget.initialTag != null && widget.initialTag!.isNotEmpty) {
      _selectedTag = widget.initialTag;
      _searchByTag = true;
      _searchByTagSlug(widget.initialTag!);
    }
    // Auto-search if query passed from nav bar
    else if (widget.initialQuery != null && widget.initialQuery!.isNotEmpty) {
      _controller.text = widget.initialQuery!;
      _search(widget.initialQuery!);
    }
  }

  @override
  void didUpdateWidget(covariant SearchScreen oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.initialTag != null &&
        widget.initialTag != oldWidget.initialTag &&
        widget.initialTag!.isNotEmpty) {
      _selectedTag = widget.initialTag;
      _searchByTag = true;
      _searchByTagSlug(widget.initialTag!);
    } else if (widget.initialQuery != null &&
        widget.initialQuery != oldWidget.initialQuery &&
        widget.initialQuery!.isNotEmpty) {
      _controller.text = widget.initialQuery!;
      _searchByTag = false;
      _selectedTag = null;
      _search(widget.initialQuery!);
    }
  }

  void _onScroll() {
    final shouldShow = _scrollController.offset > 400;
    if (shouldShow != _showScrollToTop) {
      setState(() => _showScrollToTop = shouldShow);
    }
  }

  Future<void> _fetchTags() async {
    try {
      final tags = await _api.getTags();
      if (mounted) setState(() { _tags = tags; _loadingTags = false; });
    } catch (e) {
      if (mounted) setState(() => _loadingTags = false);
    }
  }

  Future<void> _search(String query) async {
    if (query.trim().isEmpty) return;

    setState(() { _loading = true; _hasSearched = true; _searchByTag = false; _selectedTag = null; });

    try {
      final response = await _api.getPosts(search: query.trim());
      if (mounted) setState(() { _results = response.posts; _loading = false; });
    } catch (e) {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _searchByTagSlug(String tagSlug) async {
    setState(() { _loading = true; _hasSearched = true; _searchByTag = true; _selectedTag = tagSlug; _controller.clear(); });

    try {
      final response = await _api.getPosts(tag: tagSlug);
      if (mounted) setState(() { _results = response.posts; _loading = false; });
    } catch (e) {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _clearTagFilter() {
    setState(() {
      _searchByTag = false;
      _selectedTag = null;
      _results = [];
      _hasSearched = false;
    });
    context.go('/search');
  }

  @override
  void dispose() {
    _controller.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  String _getTagName(String slug) {
    final tag = _tags.where((t) => t.slug == slug);
    if (tag.isNotEmpty) return tag.first.name;
    return slug;
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    final isDesktop = Responsive.isDesktop(context);

    return Scaffold(
      backgroundColor: Colors.transparent,
      floatingActionButton: AnimatedOpacity(
        opacity: _showScrollToTop ? 1.0 : 0.0,
        duration: const Duration(milliseconds: 250),
        child: FloatingActionButton.small(
          onPressed: () => _scrollController.animateTo(0,
            duration: const Duration(milliseconds: 600),
            curve: Curves.easeOutCubic,
          ),
          backgroundColor: colorScheme.surfaceContainerHighest,
          foregroundColor: colorScheme.onSurface,
          elevation: 2,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
          child: const Icon(Icons.keyboard_arrow_up_rounded, size: 24),
        ),
      ),
      body: SingleChildScrollView(
        controller: _scrollController,
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
                  // Search bar
                  Container(
                    decoration: BoxDecoration(
                      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.3),
                      borderRadius: BorderRadius.circular(24),
                    ),
                    child: TextField(
                      controller: _controller,
                      onSubmitted: _search,
                      autofocus: widget.initialQuery == null && widget.initialTag == null,
                      decoration: InputDecoration(
                        hintText: 'Search stories...',
                        hintStyle: textTheme.bodyLarge?.copyWith(
                          color: colorScheme.onSurfaceVariant.withValues(alpha: 0.5),
                        ),
                        prefixIcon: Padding(
                          padding: const EdgeInsets.only(left: 16, right: 8),
                          child: Icon(Icons.search, color: colorScheme.onSurfaceVariant, size: 22),
                        ),
                        prefixIconConstraints: const BoxConstraints(minWidth: 46),
                        border: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                      ),
                      style: textTheme.bodyLarge,
                    ),
                  ).animate().fadeIn(duration: 300.ms),

                  const SizedBox(height: 16),

                  // Tag filter chips
                  if (!_loadingTags && _tags.isNotEmpty)
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(Icons.label_outline, size: 16, color: colorScheme.onSurfaceVariant.withValues(alpha: 0.6)),
                            const SizedBox(width: 6),
                            Text(
                              'Search by tag:',
                              style: textTheme.labelMedium?.copyWith(
                                color: colorScheme.onSurfaceVariant.withValues(alpha: 0.6),
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Wrap(
                          spacing: 6,
                          runSpacing: 6,
                          children: _tags.map((tag) {
                            final isSelected = _selectedTag == tag.slug;
                            return _SearchTagChip(
                              label: '#${tag.name}',
                              isSelected: isSelected,
                              onTap: () {
                                if (isSelected) {
                                  _clearTagFilter();
                                } else {
                                  context.go('/search?tag=${tag.slug}');
                                  _searchByTagSlug(tag.slug);
                                }
                              },
                              colorScheme: colorScheme,
                            );
                          }).toList(),
                        ),
                      ],
                    ).animate().fadeIn(duration: 300.ms, delay: 100.ms),

                  const SizedBox(height: 24),

                  // Active tag filter indicator
                  if (_searchByTag && _selectedTag != null && !_loading)
                    Container(
                      margin: const EdgeInsets.only(bottom: 16),
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                      decoration: BoxDecoration(
                        color: colorScheme.primaryContainer.withValues(alpha: 0.3),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: colorScheme.primary.withValues(alpha: 0.2),
                        ),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.label_rounded, size: 18, color: colorScheme.primary),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'Showing posts tagged #${_getTagName(_selectedTag!)}',
                              style: textTheme.bodySmall?.copyWith(
                                color: colorScheme.primary,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ),
                          GestureDetector(
                            onTap: _clearTagFilter,
                            child: MouseRegion(
                              cursor: SystemMouseCursors.click,
                              child: Icon(Icons.close_rounded, size: 18, color: colorScheme.primary),
                            ),
                          ),
                        ],
                      ),
                    ).animate().fadeIn(duration: 200.ms),

                  // Results
                  if (_loading)
                    const ShimmerPostList(count: 4)
                  else if (_hasSearched && _results.isEmpty)
                    Center(
                      child: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 60),
                        child: Column(
                          children: [
                            Icon(
                              Icons.search_off_rounded,
                              size: 48,
                              color: colorScheme.onSurfaceVariant.withValues(alpha: 0.4),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              'No results found',
                              style: textTheme.titleMedium?.copyWith(
                                color: colorScheme.onSurfaceVariant,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              _searchByTag
                                  ? 'No stories found with this tag.'
                                  : 'Try a different search term.',
                              style: textTheme.bodyMedium?.copyWith(
                                color: colorScheme.onSurfaceVariant.withValues(alpha: 0.7),
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                  else if (_results.isNotEmpty)
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          _searchByTag
                              ? '${_results.length} post${_results.length == 1 ? '' : 's'} tagged #${_getTagName(_selectedTag!)}'
                              : '${_results.length} result${_results.length == 1 ? '' : 's'} for "${_controller.text}"',
                          style: textTheme.bodySmall?.copyWith(
                            color: colorScheme.onSurfaceVariant,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
                        ListView.separated(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: _results.length,
                          separatorBuilder: (context, index) => Divider(
                            color: colorScheme.outlineVariant.withValues(alpha: 0.2),
                            height: 1,
                          ),
                          itemBuilder: (context, index) {
                            return AnimatedListItem(
                              index: index,
                              child: PostCard(post: _results[index]),
                            );
                          },
                        ),
                      ],
                    ),

                  const SizedBox(height: 40),
                  const AppFooter(),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _SearchTagChip extends StatefulWidget {
  final String label;
  final bool isSelected;
  final VoidCallback onTap;
  final ColorScheme colorScheme;

  const _SearchTagChip({
    required this.label,
    required this.isSelected,
    required this.onTap,
    required this.colorScheme,
  });

  @override
  State<_SearchTagChip> createState() => _SearchTagChipState();
}

class _SearchTagChipState extends State<_SearchTagChip> {
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
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
          decoration: BoxDecoration(
            color: widget.isSelected
                ? widget.colorScheme.primary
                : _isHovered
                    ? widget.colorScheme.surfaceContainerHighest
                    : Colors.transparent,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: widget.isSelected
                  ? widget.colorScheme.primary
                  : widget.colorScheme.outlineVariant.withValues(alpha: 0.5),
            ),
          ),
          child: Text(
            widget.label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: widget.isSelected ? FontWeight.w600 : FontWeight.w500,
              color: widget.isSelected
                  ? widget.colorScheme.onPrimary
                  : widget.colorScheme.onSurfaceVariant,
            ),
          ),
        ),
      ),
    );
  }
}
