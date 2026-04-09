import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;

class CookieConsentSheet extends StatefulWidget {
  const CookieConsentSheet({super.key});

  @override
  State<CookieConsentSheet> createState() => _CookieConsentSheetState();
}

class _CookieConsentSheetState extends State<CookieConsentSheet> {
  bool _isVisible = false;
  final TextEditingController _emailController = TextEditingController();
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _checkConsent();
  }

  Future<void> _checkConsent() async {
    final prefs = await SharedPreferences.getInstance();
    final bool hasConsented = prefs.getBool('cookie_consent_acted') ?? false;
    
    // Slight delay to allow splash screen to yield gracefully
    if (!hasConsented) {
      await Future.delayed(const Duration(seconds: 4));
      if (mounted) {
        setState(() {
          _isVisible = true;
        });
      }
    }
  }

  Future<void> _handleConsent(bool accepted) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('cookie_consent_acted', true);
    await prefs.setBool('cookie_consent_accepted', accepted);
    setState(() {
      _isVisible = false;
    });
  }

  Future<void> _subscribeEmail() async {
    final email = _emailController.text.trim();
    if (email.isEmpty || !email.contains('@')) return;

    setState(() => _isLoading = true);

    try {
      final res = await http.post(
        Uri.parse('http://localhost:8080/api/tracking/email-subscribe'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email})
      );
      
      if (res.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Successfully Subscribed!')),
        );
        _emailController.clear();
        await _handleConsent(true); // Treat subscription as active engagement/consent
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Network Error.')),
      );
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  void dispose() {
    _emailController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (!_isVisible) return const SizedBox.shrink();

    final theme = Theme.of(context);
    final colorScheme = theme.colorScheme;

    return Positioned(
      bottom: 24,
      left: 24,
      right: 24,
      child: Center(
        child: Container(
          constraints: const BoxConstraints(maxWidth: 600),
          decoration: BoxDecoration(
            color: colorScheme.surface,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.2),
                blurRadius: 20,
                offset: const Offset(0, 10),
              )
            ],
            border: Border.all(
              color: colorScheme.outline.withValues(alpha: 0.1),
            ),
          ),
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(Icons.cookie_outlined, color: colorScheme.primary, size: 28),
                  const SizedBox(width: 12),
                  Text(
                    'We value your experience',
                    style: theme.textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Text(
                'We use cookies to seamlessly identify platform issues, detect system bugs, and rapidly enhance your webapp experience from time to time.',
                style: theme.textTheme.bodyMedium?.copyWith(
                  color: colorScheme.onSurfaceVariant,
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 24),
              Text(
                'Stay Informed',
                style: theme.textTheme.titleSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: _emailController,
                      decoration: InputDecoration(
                        hintText: 'Enter your email for new posts...',
                        isDense: true,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide(color: colorScheme.outline),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  ElevatedButton(
                    onPressed: _isLoading ? null : _subscribeEmail,
                    style: ElevatedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                      backgroundColor: colorScheme.primary,
                      foregroundColor: colorScheme.onPrimary,
                    ),
                    child: _isLoading 
                      ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2)) 
                      : const Text('Subscribe'),
                  ),
                ],
              ),
              const SizedBox(height: 24),
              Wrap(
                alignment: WrapAlignment.end,
                spacing: 12,
                runSpacing: 12,
                children: [
                  TextButton(
                    onPressed: () => _handleConsent(false),
                    style: TextButton.styleFrom(
                      foregroundColor: colorScheme.onSurfaceVariant,
                    ),
                    child: const Text('Strictly Necessary Only'),
                  ),
                  FilledButton(
                    onPressed: () => _handleConsent(true),
                    style: FilledButton.styleFrom(
                      backgroundColor: colorScheme.secondary,
                      foregroundColor: colorScheme.onSecondary,
                    ),
                    child: const Text('Accept All Cookies'),
                  ),
                ],
              )
            ],
          ),
        ),
      ),
    );
  }
}
