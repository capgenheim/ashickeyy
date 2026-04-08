import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../services/api_service.dart';
import '../models/post.dart';
import '../widgets/post_card.dart';
import '../widgets/category_carousel.dart';
import '../widgets/hero_banner.dart';
import '../widgets/shimmer_loader.dart';
import '../widgets/footer.dart';
import '../widgets/animated_list_item.dart';
import '../utils/responsive.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ApiService _api = ApiService();
  final ScrollController _scrollController = ScrollController();

  List<Post> _posts = [];
  bool _loading = true;
  bool _loadingMore = false;
  bool _hasMore = true;
  String? _cursor;
  String? _selectedCategory;
  bool _showScrollToTop = false;

  @override
  void initState() {
    super.initState();
    _fetchPosts();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    // Infinite scroll trigger
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 500) {
      _fetchMorePosts();
    }
    // Show/hide scroll-to-top button
    final shouldShow = _scrollController.offset > 600;
    if (shouldShow != _showScrollToTop) {
      setState(() => _showScrollToTop = shouldShow);
    }
  }

  void _scrollToTop() {
    _scrollController.animateTo(
      0,
      duration: const Duration(milliseconds: 600),
      curve: Curves.easeOutCubic,
    );
  }

  Future<void> _fetchPosts() async {
    try {
      final response = await _api.getPosts(category: _selectedCategory);
      if (mounted) {
        setState(() {
          _posts = response.posts;
          _cursor = response.nextCursor;
          _hasMore = response.hasMore;
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _fetchMorePosts() async {
    if (_loadingMore || !_hasMore || _cursor == null) return;
    setState(() => _loadingMore = true);

    try {
      final response = await _api.getPosts(cursor: _cursor, category: _selectedCategory);
      if (mounted) {
        setState(() {
          _posts.addAll(response.posts);
          _cursor = response.nextCursor;
          _hasMore = response.hasMore;
          _loadingMore = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _loadingMore = false);
    }
  }

  Future<void> _refresh() async {
    _api.clearCache();
    setState(() {
      _posts = [];
      _cursor = null;
      _hasMore = true;
      _loading = true;
    });
    await _fetchPosts();
  }

  void _onCategorySelected(String? slug) {
    if (_selectedCategory == slug) return;
    setState(() {
      _selectedCategory = slug;
      _posts = [];
      _cursor = null;
      _hasMore = true;
      _loading = true;
    });
    _fetchPosts();
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
        child: AnimatedSlide(
          offset: _showScrollToTop ? Offset.zero : const Offset(0, 1),
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeOut,
          child: FloatingActionButton.small(
            onPressed: _scrollToTop,
            backgroundColor: colorScheme.surfaceContainerHighest,
            foregroundColor: colorScheme.onSurface,
            elevation: 2,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            child: const Icon(Icons.keyboard_arrow_up_rounded, size: 24),
          ),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: _refresh,
        child: CustomScrollView(
          controller: _scrollController,
          physics: const AlwaysScrollableScrollPhysics(),
          slivers: [
            // Hero Banner
            SliverToBoxAdapter(
              child: Center(
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 700),
                  child: Padding(
                    padding: EdgeInsets.symmetric(horizontal: isDesktop ? 0 : 24),
                    child: const HeroBanner(),
                  ),
                ),
              ),
            ),

            // Main content
            SliverToBoxAdapter(
              child: Center(
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 700),
                  child: Padding(
                    padding: EdgeInsets.symmetric(horizontal: isDesktop ? 0 : 24),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const SizedBox(height: 8),

                        // Category tabs
                        Padding(
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          child: CategoryCarousel(
                            selectedSlug: _selectedCategory,
                            onCategorySelected: _onCategorySelected,
                          ),
                        ),

                        Divider(
                          color: colorScheme.outlineVariant.withValues(alpha: 0.3),
                          height: 1,
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),

            // Loading state
            if (_loading)
              SliverToBoxAdapter(
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 700),
                    child: Padding(
                      padding: EdgeInsets.symmetric(
                        horizontal: isDesktop ? 0 : 24,
                        vertical: 24,
                      ),
                      child: const ShimmerPostList(count: 5),
                    ),
                  ),
                ),
              ),

            // Empty state
            if (!_loading && _posts.isEmpty)
              SliverToBoxAdapter(
                child: _buildEmptyState(colorScheme, textTheme),
              ),

            // Highlighted new posts (first 5)
            if (!_loading && _posts.isNotEmpty && _selectedCategory == null)
              SliverToBoxAdapter(
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 700),
                    child: Padding(
                      padding: EdgeInsets.symmetric(horizontal: isDesktop ? 0 : 24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // "New" badge section
                          Padding(
                            padding: const EdgeInsets.only(top: 24, bottom: 4),
                            child: Row(
                              children: [
                                Container(
                                  width: 8,
                                  height: 8,
                                  decoration: BoxDecoration(
                                    color: colorScheme.primary,
                                    shape: BoxShape.circle,
                                  ),
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'New',
                                  style: textTheme.labelLarge?.copyWith(
                                    fontWeight: FontWeight.w700,
                                    color: colorScheme.primary,
                                    letterSpacing: 0.5,
                                  ),
                                ),
                              ],
                            ),
                          ).animate().fadeIn(duration: 400.ms),

                          // First 5 posts highlighted
                          ...List.generate(
                            _posts.length < 5 ? _posts.length : 5,
                            (index) => Column(
                              children: [
                                AnimatedListItem(
                                  index: index,
                                  child: Container(
                                    decoration: index == 0
                                        ? BoxDecoration(
                                            border: Border(
                                              left: BorderSide(
                                                color: colorScheme.primary.withValues(alpha: 0.4),
                                                width: 3,
                                              ),
                                            ),
                                          )
                                        : null,
                                    padding: index == 0
                                        ? const EdgeInsets.only(left: 12)
                                        : EdgeInsets.zero,
                                    child: PostCard(
                                      post: _posts[index],
                                      isFeatured: index == 0,
                                    ),
                                  ),
                                ),
                                Divider(
                                  color: colorScheme.outlineVariant.withValues(alpha: 0.2),
                                  height: 1,
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),

            // Remaining posts
            if (!_loading && _posts.length > 5 && _selectedCategory == null)
              SliverToBoxAdapter(
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 700),
                    child: Padding(
                      padding: EdgeInsets.symmetric(horizontal: isDesktop ? 0 : 24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Padding(
                            padding: const EdgeInsets.only(top: 24, bottom: 4),
                            child: Text(
                              'More stories',
                              style: textTheme.labelLarge?.copyWith(
                                fontWeight: FontWeight.w600,
                                color: colorScheme.onSurfaceVariant,
                              ),
                            ),
                          ).animate().fadeIn(duration: 400.ms),
                          Divider(
                            color: colorScheme.outlineVariant.withValues(alpha: 0.3),
                            height: 1,
                          ),
                          ...List.generate(
                            _posts.length - 5,
                            (i) {
                              final index = i + 5;
                              return Column(
                                children: [
                                  AnimatedListItem(
                                    index: index,
                                    child: PostCard(post: _posts[index]),
                                  ),
                                  Divider(
                                    color: colorScheme.outlineVariant.withValues(alpha: 0.2),
                                    height: 1,
                                  ),
                                ],
                              );
                            },
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),

            // Category-filtered posts (no highlight split)
            if (!_loading && _posts.isNotEmpty && _selectedCategory != null)
              SliverToBoxAdapter(
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 700),
                    child: Padding(
                      padding: EdgeInsets.symmetric(horizontal: isDesktop ? 0 : 24),
                      child: ListView.separated(
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
                            child: PostCard(
                              post: _posts[index],
                              isFeatured: index == 0,
                            ),
                          );
                        },
                      ),
                    ),
                  ),
                ),
              ),

            // Loading more indicator
            if (_loadingMore)
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.symmetric(vertical: 32),
                  child: Center(
                    child: SizedBox(
                      width: 24,
                      height: 24,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        color: colorScheme.primary,
                      ),
                    ),
                  ),
                ),
              ),

            // End of feed
            if (!_hasMore && _posts.isNotEmpty && !_loading)
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.symmetric(vertical: 32),
                  child: Center(
                    child: Text(
                      '— You\'ve reached the end —',
                      style: textTheme.bodySmall?.copyWith(
                        color: colorScheme.onSurfaceVariant.withValues(alpha: 0.6),
                        fontStyle: FontStyle.italic,
                      ),
                    ),
                  ),
                ),
              ),

            // Footer
            SliverToBoxAdapter(
              child: Center(
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 700),
                  child: Padding(
                    padding: EdgeInsets.symmetric(horizontal: isDesktop ? 0 : 24),
                    child: const AppFooter(),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyState(ColorScheme colorScheme, TextTheme textTheme) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 80),
        child: Column(
          children: [
            Icon(
              Icons.article_outlined,
              size: 48,
              color: colorScheme.onSurfaceVariant.withValues(alpha: 0.4),
            ),
            const SizedBox(height: 16),
            Text(
              'No stories yet',
              style: textTheme.titleMedium?.copyWith(
                color: colorScheme.onSurfaceVariant,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Check back soon for new content.',
              style: textTheme.bodyMedium?.copyWith(
                color: colorScheme.onSurfaceVariant.withValues(alpha: 0.7),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
