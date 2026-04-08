import 'package:flutter/material.dart';
import '../models/tag.dart';
import '../services/api_service.dart';

class TagCarousel extends StatefulWidget {
  final String? selectedSlug;
  final ValueChanged<String?> onTagSelected;

  const TagCarousel({
    super.key,
    this.selectedSlug,
    required this.onTagSelected,
  });

  @override
  State<TagCarousel> createState() => _TagCarouselState();
}

class _TagCarouselState extends State<TagCarousel> {
  final ApiService _api = ApiService();
  List<Tag> _tags = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _fetchTags();
  }

  Future<void> _fetchTags() async {
    try {
      final tags = await _api.getTags();
      if (mounted) {
        setState(() {
          _tags = tags;
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const SizedBox(height: 40);
    }

    if (_tags.isEmpty) return const SizedBox.shrink();

    final colorScheme = Theme.of(context).colorScheme;

    return SizedBox(
      height: 40,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemCount: _tags.length + 1,
        separatorBuilder: (context, index) => const SizedBox(width: 4),
        itemBuilder: (context, index) {
          if (index == 0) {
            return _TabChip(
              label: 'All topics',
              isSelected: widget.selectedSlug == null,
              onTap: () => widget.onTagSelected(null),
              colorScheme: colorScheme,
            );
          }

          final tag = _tags[index - 1];
          return _TabChip(
            label: '#${tag.name}',
            isSelected: widget.selectedSlug == tag.slug,
            onTap: () => widget.onTagSelected(tag.slug),
            colorScheme: colorScheme,
          );
        },
      ),
    );
  }
}

class _TabChip extends StatefulWidget {
  final String label;
  final bool isSelected;
  final VoidCallback onTap;
  final ColorScheme colorScheme;

  const _TabChip({
    required this.label,
    required this.isSelected,
    required this.onTap,
    required this.colorScheme,
  });

  @override
  State<_TabChip> createState() => _TabChipState();
}

class _TabChipState extends State<_TabChip> {
  bool _isHovered = false;

  @override
  Widget build(BuildContext context) {
    return MouseRegion(
      cursor: SystemMouseCursors.click,
      onEnter: (_) => setState(() => _isHovered = true),
      onExit: (_) => setState(() => _isHovered = false),
      child: GestureDetector(
        onTap: widget.onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(20),
            color: widget.isSelected
                ? widget.colorScheme.primary
                : _isHovered
                    ? widget.colorScheme.surfaceContainerHighest
                    : Colors.transparent,
            border: Border.all(
              color: widget.isSelected 
                  ? widget.colorScheme.primary 
                  : widget.colorScheme.outlineVariant.withValues(alpha: 0.5)
            )
          ),
          child: Center(
            child: Text(
              widget.label,
              style: TextStyle(
                fontSize: 13,
                fontWeight: widget.isSelected ? FontWeight.w600 : FontWeight.w500,
                color: widget.isSelected
                    ? widget.colorScheme.onPrimary
                    : widget.colorScheme.onSurfaceVariant,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
