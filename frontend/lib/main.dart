import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_strategy/url_strategy.dart';
import 'config/theme.dart';
import 'config/routes.dart';
import 'services/theme_service.dart';
import 'widgets/splash_overlay.dart';
import 'widgets/cookie_consent_sheet.dart';
import 'services/tracking_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  setPathUrlStrategy(); // Remove # from URLs
  
  // Handshake Geolocation & PWA Push
  TrackingService.initializeTracking();

  runApp(const AshickeyApp());
}

class AshickeyApp extends StatelessWidget {
  const AshickeyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (_) => ThemeService(),
      child: Consumer<ThemeService>(
        builder: (context, themeService, _) {
          return MaterialApp.router(
            title: 'ashickey{}',
            debugShowCheckedModeBanner: false,
            theme: AppTheme.lightTheme,
            darkTheme: AppTheme.darkTheme,
            themeMode: themeService.themeMode,
            routerConfig: appRouter,
            builder: (context, child) {
              return Stack(
                children: [
                  child!,
                  const SplashOverlay(),
                  const CookieConsentSheet(),
                ],
              );
            },
          );
        },
      ),
    );
  }
}
