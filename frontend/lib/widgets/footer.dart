import 'package:flutter/material.dart';

class AppFooter extends StatelessWidget {
  const AppFooter({super.key});

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 40, horizontal: 24),
      decoration: BoxDecoration(
        border: Border(
          top: BorderSide(
            color: colorScheme.outlineVariant.withValues(alpha: 0.3),
          ),
        ),
      ),
      child: Column(
        children: [
          // Logo
          Text(
            'ashickey{}',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.w900,
              letterSpacing: -0.5,
              color: colorScheme.onSurface,
            ),
          ),
          const SizedBox(height: 12),
          // Links row
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              _FooterLink(label: 'Home', colorScheme: colorScheme),
              _FooterDot(colorScheme: colorScheme),
              _FooterLink(label: 'About', colorScheme: colorScheme),
              _FooterDot(colorScheme: colorScheme),
              _FooterLink(
                label: 'Tags',
                colorScheme: colorScheme,
              ),
            ],
          ),
          const SizedBox(height: 20),
          Text(
            '© ${DateTime.now().year} ashickey{}. All rights reserved.',
            style: TextStyle(
              fontSize: 12,
              color: colorScheme.onSurfaceVariant.withValues(alpha: 0.5),
            ),
          ),
        ],
      ),
    );
  }
}

class _FooterLink extends StatelessWidget {
  final String label;
  final ColorScheme colorScheme;
  const _FooterLink({required this.label, required this.colorScheme});

  @override
  Widget build(BuildContext context) {
    return Text(
      label,
      style: TextStyle(
        fontSize: 13,
        color: colorScheme.onSurfaceVariant.withValues(alpha: 0.7),
        fontWeight: FontWeight.w500,
      ),
    );
  }
}

class _FooterDot extends StatelessWidget {
  final ColorScheme colorScheme;
  const _FooterDot({required this.colorScheme});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      child: Text(
        '·',
        style: TextStyle(
          color: colorScheme.onSurfaceVariant.withValues(alpha: 0.4),
          fontWeight: FontWeight.w900,
        ),
      ),
    );
  }
}
