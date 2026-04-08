import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../widgets/footer.dart';
import '../utils/responsive.dart';

class AboutScreen extends StatelessWidget {
  const AboutScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    final isDesktop = Responsive.isDesktop(context);

    return SingleChildScrollView(
      child: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 680),
          child: Padding(
            padding: EdgeInsets.symmetric(
              horizontal: isDesktop ? 0 : 24,
              vertical: 48,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Author avatar + name
                Center(
                  child: Column(
                    children: [
                      Container(
                        width: 88,
                        height: 88,
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                            colors: [colorScheme.primary, colorScheme.tertiary],
                          ),
                          shape: BoxShape.circle,
                        ),
                        child: Center(
                          child: Text(
                            'A',
                            style: textTheme.displaySmall?.copyWith(
                              color: colorScheme.onPrimary,
                              fontWeight: FontWeight.w900,
                              fontSize: 32,
                            ),
                          ),
                        ),
                      )
                          .animate()
                          .fadeIn(duration: 500.ms)
                          .scale(begin: const Offset(0.9, 0.9), end: const Offset(1, 1), duration: 500.ms),
                      const SizedBox(height: 20),
                      Text(
                        'ashickey{}',
                        style: textTheme.headlineMedium?.copyWith(
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.5,
                        ),
                      ).animate().fadeIn(duration: 500.ms, delay: 150.ms),
                      const SizedBox(height: 8),
                      Text(
                        'Writer · Developer · Thinker',
                        style: textTheme.bodyLarge?.copyWith(
                          color: colorScheme.onSurfaceVariant,
                        ),
                      ).animate().fadeIn(duration: 500.ms, delay: 250.ms),
                    ],
                  ),
                ),
                const SizedBox(height: 48),
                Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
                const SizedBox(height: 40),

                // About section
                Text(
                  'Welcome to ashickey{} — a space where technology meets simplicity. '
                  'This blog covers current issues in tech, beginner-friendly development tutorials, '
                  'and general thoughts on the digital world around us.',
                  style: textTheme.bodyLarge?.copyWith(
                    height: 1.8,
                    fontSize: 18,
                    color: colorScheme.onSurface,
                  ),
                ).animate().fadeIn(duration: 500.ms, delay: 400.ms),
                const SizedBox(height: 32),

                Text(
                  'What you\'ll find here',
                  style: textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 16),

                _TopicRow(
                  icon: Icons.devices_rounded,
                  title: 'Technology',
                  description: 'The latest trends and insights in tech',
                  colorScheme: colorScheme,
                  textTheme: textTheme,
                ),
                _TopicRow(
                  icon: Icons.code_rounded,
                  title: 'Development',
                  description: 'Tutorials and guides for builders',
                  colorScheme: colorScheme,
                  textTheme: textTheme,
                ),
                _TopicRow(
                  icon: Icons.public_rounded,
                  title: 'Current Issues',
                  description: 'Analysis of what\'s happening in the world',
                  colorScheme: colorScheme,
                  textTheme: textTheme,
                ),
                _TopicRow(
                  icon: Icons.lightbulb_outline_rounded,
                  title: 'General',
                  description: 'Thoughts, opinions, and everything in between',
                  colorScheme: colorScheme,
                  textTheme: textTheme,
                ),

                const SizedBox(height: 40),
                Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
                const SizedBox(height: 32),

                // Built with section
                Center(
                  child: Text(
                    'Built with Flutter · Laravel · MongoDB',
                    style: textTheme.bodySmall?.copyWith(
                      color: colorScheme.onSurfaceVariant.withValues(alpha: 0.6),
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

class _TopicRow extends StatelessWidget {
  final IconData icon;
  final String title;
  final String description;
  final ColorScheme colorScheme;
  final TextTheme textTheme;

  const _TopicRow({
    required this.icon,
    required this.title,
    required this.description,
    required this.colorScheme,
    required this.textTheme,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 10),
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, size: 20, color: colorScheme.primary),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: textTheme.bodyLarge?.copyWith(fontWeight: FontWeight.w600),
                ),
                Text(
                  description,
                  style: textTheme.bodySmall?.copyWith(
                    color: colorScheme.onSurfaceVariant,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    ).animate().fadeIn(duration: 400.ms, delay: 500.ms);
  }
}
