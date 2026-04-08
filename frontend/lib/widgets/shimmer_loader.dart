import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';

class ShimmerPostCard extends StatelessWidget {
  const ShimmerPostCard({super.key});

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final baseColor = colorScheme.surfaceContainerHighest;
    final highlightColor = colorScheme.surface;

    return Card(
      child: Shimmer.fromColors(
        baseColor: baseColor,
        highlightColor: highlightColor,
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Image placeholder
              Container(
                height: 180,
                decoration: BoxDecoration(
                  color: baseColor,
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              const SizedBox(height: 16),
              // Category badge
              Container(height: 20, width: 80, color: baseColor),
              const SizedBox(height: 12),
              // Title
              Container(height: 22, width: double.infinity, color: baseColor),
              const SizedBox(height: 6),
              Container(height: 22, width: 200, color: baseColor),
              const SizedBox(height: 12),
              // Excerpt
              Container(height: 14, width: double.infinity, color: baseColor),
              const SizedBox(height: 4),
              Container(height: 14, width: 250, color: baseColor),
              const SizedBox(height: 16),
              // Date
              Container(height: 12, width: 120, color: baseColor),
            ],
          ),
        ),
      ),
    );
  }
}

class ShimmerPostList extends StatelessWidget {
  final int count;

  const ShimmerPostList({super.key, this.count = 3});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(count, (_) => const Padding(
        padding: EdgeInsets.only(bottom: 16),
        child: ShimmerPostCard(),
      )),
    );
  }
}
