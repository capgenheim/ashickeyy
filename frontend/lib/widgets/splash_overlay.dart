import 'package:flutter/material.dart';
import 'dart:ui';

class SplashOverlay extends StatefulWidget {
  const SplashOverlay({super.key});

  @override
  State<SplashOverlay> createState() => _SplashOverlayState();
}

class _SplashOverlayState extends State<SplashOverlay>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<Offset> _slideAnimation;
  bool _isVisible = true;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    );

    _slideAnimation = Tween<Offset>(
      begin: Offset.zero,
      end: const Offset(0, -1.0), // Slide up by full height
    ).animate(CurvedAnimation(
      parent: _controller,
      curve: Curves.easeInOutCubic,
    ));

    // Delay 3 seconds, then start slide animation
    Future.delayed(const Duration(seconds: 3), () {
      if (mounted) {
        _controller.forward().then((_) {
          setState(() {
            _isVisible = false; // Remove completely from tree after animation
          });
        });
      }
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // The Splash Screen Overlay
    if (!_isVisible) return const SizedBox.shrink();

    return SlideTransition(
            position: _slideAnimation,
            child: ClipRect(
              child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
                child: Container(
                  width: MediaQuery.of(context).size.width,
                  height: MediaQuery.of(context).size.height,
                  decoration: BoxDecoration(
                    color: const Color(0xFF0F1117).withValues(alpha: 0.4),
                    gradient: LinearGradient(
                      colors: [
                        const Color(0xFF0F1117).withValues(alpha: 0.5),
                        const Color(0xFF1E1B4B).withValues(alpha: 0.3),
                        const Color(0xFF0F1117).withValues(alpha: 0.5),
                      ],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    border: Border.all(
                      color: Colors.white.withValues(alpha: 0.1),
                    ),
                  ),
                  child: Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          'ashickey{}',
                          style: TextStyle(
                            fontSize: 32,
                            fontWeight: FontWeight.bold,
                            color: Colors.blue.shade400,
                            letterSpacing: -0.5,
                            decoration: TextDecoration.none,
                            shadows: [
                              Shadow(
                                color: Colors.blue.withValues(alpha: 0.5),
                                blurRadius: 20,
                              )
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          );
  }
}
