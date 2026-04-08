import 'package:flutter/material.dart';
import 'package:flutter_markdown/flutter_markdown.dart';
import 'package:url_launcher/url_launcher_string.dart';

class MarkdownView extends StatelessWidget {
  final String data;

  const MarkdownView({super.key, required this.data});

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    return MarkdownBody(
      data: data,
      selectable: true,
      onTapLink: (text, href, title) {
        if (href != null) launchUrlString(href);
      },
      styleSheet: MarkdownStyleSheet(
        h1: textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.w800, height: 1.4),
        h2: textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w700, height: 1.4),
        h3: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w700, height: 1.4),
        h4: textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w600, height: 1.4),
        p: textTheme.bodyLarge?.copyWith(height: 1.8, fontSize: 17),
        a: TextStyle(color: colorScheme.primary, decoration: TextDecoration.underline),
        code: TextStyle(
          backgroundColor: colorScheme.surfaceContainerHighest,
          fontFamily: 'monospace',
          fontSize: 14,
        ),
        codeblockDecoration: BoxDecoration(
          color: colorScheme.surfaceContainerHighest,
          borderRadius: BorderRadius.circular(12),
        ),
        codeblockPadding: const EdgeInsets.all(16),
        blockquoteDecoration: BoxDecoration(
          border: Border(
            left: BorderSide(color: colorScheme.primary, width: 4),
          ),
        ),
        blockquotePadding: const EdgeInsets.only(left: 16, top: 8, bottom: 8),
        listBullet: textTheme.bodyLarge?.copyWith(height: 1.8),
        horizontalRuleDecoration: BoxDecoration(
          border: Border(
            top: BorderSide(color: colorScheme.outlineVariant, width: 1),
          ),
        ),
      ),
    );
  }
}
