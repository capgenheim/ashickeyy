import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';

class HeroBanner extends StatelessWidget {
  const HeroBanner({super.key});

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 48, horizontal: 0),
      decoration: BoxDecoration(
        border: Border(
          bottom: BorderSide(
            color: colorScheme.outlineVariant.withValues(alpha: 0.3),
            width: 1,
          ),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Stay curious.',
            style: textTheme.displayMedium?.copyWith(
              fontWeight: FontWeight.w400,
              letterSpacing: -1.5,
              height: 1.15,
              color: colorScheme.onSurface,
              fontFamily: 'Georgia',
            ),
          )
              .animate()
              .fadeIn(duration: 800.ms)
              .slideX(begin: -0.03, end: 0, duration: 800.ms, curve: Curves.easeOut),
          const SizedBox(height: 16),
          Text(
            'Discover stories, thinking, and expertise from writers on technology, development, and the world around us.',
            style: textTheme.titleMedium?.copyWith(
              color: colorScheme.onSurfaceVariant,
              height: 1.6,
              fontWeight: FontWeight.w400,
            ),
          )
              .animate(delay: 200.ms)
              .fadeIn(duration: 600.ms),
          const SizedBox(height: 28),
          FilledButton(
            onPressed: () {},
            style: FilledButton.styleFrom(
              backgroundColor: isDark ? colorScheme.primary : colorScheme.onSurface,
              foregroundColor: isDark ? colorScheme.onPrimary : colorScheme.surface,
              padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 14),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(24),
              ),
              textStyle: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w500,
                letterSpacing: 0.2,
              ),
            ),
            child: const Text('Start reading'),
          )
              .animate(delay: 400.ms)
              .fadeIn(duration: 500.ms)
              .slideY(begin: 0.15, end: 0, duration: 500.ms, curve: Curves.easeOut),
        ],
      ),
    );
  }
}
