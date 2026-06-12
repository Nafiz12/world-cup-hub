from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, ListFlowable, ListItem, PageBreak
from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT, TA_CENTER
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.pdfbase import pdfmetrics

pdf_path = r"d:\word-cup-hub\live_scores_fix_report_detailed.pdf"

# Try to register a common font for better Unicode support on Windows.
try:
    pdfmetrics.registerFont(TTFont('DejaVuSans', 'C:/Windows/Fonts/arial.ttf'))
    font_name = 'DejaVuSans'
except Exception:
    font_name = 'Helvetica'

styles = getSampleStyleSheet()
styles.add(ParagraphStyle(name='ReportHeading2', parent=styles['Heading2'], fontName=font_name, spaceBefore=12, spaceAfter=6, textColor=colors.HexColor('#0f172a')))
styles.add(ParagraphStyle(name='ReportBody', parent=styles['BodyText'], fontName=font_name, fontSize=10, leading=14, textColor=colors.HexColor('#1f2937')))
styles.add(ParagraphStyle(name='ReportCode', parent=styles['Code'], fontName=font_name, fontSize=9, textColor=colors.HexColor('#111827'), backColor=colors.HexColor('#f3f4f6'), leading=12))
styles.add(ParagraphStyle(name='ReportCaption', parent=styles['BodyText'], fontName=font_name, fontSize=9, textColor=colors.HexColor('#475569')))
styles.add(ParagraphStyle(name='ReportTitle', parent=styles['Heading1'], fontName=font_name, alignment=TA_CENTER, textColor=colors.HexColor('#0b1220')))

story = []
story.append(Paragraph('Live Scores Fix Report', styles['ReportTitle']))
story.append(Spacer(1, 10))
story.append(Paragraph('Generated for the FIFA World Cup 2026 live-score issue in the Vue + Laravel project.', styles['ReportCaption']))
story.append(Spacer(1, 18))

story.append(Paragraph('1. What was not working', styles['ReportHeading2']))
story.append(Paragraph('The live-score card was showing the wrong feed and the browser path was not reliable for third-party data.', styles['ReportBody']))
story.append(Spacer(1, 6))
items = [
    'Direct browser requests to third-party live-score APIs were blocked by CORS, so the frontend could not fetch the data directly.',
    'The old live-score source returned 2022 World Cup matches, not the 2026 tournament data the page was supposed to show.',
    'The frontend card was therefore loading a feed that looked unrelated or outdated.',
]
story.append(ListFlowable([ListItem(Paragraph(x, styles['ReportBody'])) for x in items], bulletType='bullet', leftIndent=18, bulletOffsetY=0))
story.append(Spacer(1, 12))

story.append(Paragraph('2. What changed', styles['ReportHeading2']))
story.append(Paragraph('The fix was split into two real changes: use a backend proxy for external data, and switch the source to the official FIFA 2026 calendar API.', styles['ReportBody']))
story.append(Spacer(1, 8))
story.append(Paragraph('Detailed file-by-file changes:', styles['ReportBody']))
story.append(Paragraph('• backend/routes/api.php: lines 20-64 — added the /api/live-scores backend proxy, switched the source to FIFA calendar API for 2026-06-11 to 2026-07-19, filtered only World Cup competitions, and normalized the returned match objects for the frontend.', styles['ReportBody']))
story.append(Paragraph('• frontend/wc_hub/src/components/LiveScores.vue: lines 57, 109-132 — the card now calls /api/live-scores from the backend, normalizes score fields, renders the status text, and refreshes every 60 seconds.', styles['ReportBody']))
story.append(Paragraph('• backend/tests/Feature/LiveScoresRouteTest.php: lines 8-43 — added a regression test that mocks the FIFA feed and verifies only the World Cup 2026 match is returned.', styles['ReportBody']))
story.append(Spacer(1, 8))
story.append(Paragraph('A. Backend proxy for live scores', styles['ReportBody']))
story.append(Paragraph('The frontend now requests /api/live-scores from the Laravel backend instead of calling a third-party URL directly from the browser.', styles['ReportBody']))
story.append(Paragraph('This change is in: backend/routes/api.php and the frontend consumer in frontend/wc_hub/src/components/LiveScores.vue.', styles['ReportCaption']))
story.append(Spacer(1, 6))
story.append(Paragraph('B. FIFA 2026-specific source', styles['ReportBody']))
story.append(Paragraph('The backend route now calls the FIFA calendar endpoint for 2026 and filters only matches whose competition name contains “World Cup”.', styles['ReportBody']))
story.append(Spacer(1, 6))
story.append(Paragraph('Example of the backend logic added:', styles['ReportBody']))
story.append(Paragraph("""
Route::get('/live-scores', function () {
    $response = Http::timeout(20)->get('https://api.fifa.com/api/v3/calendar/matches', [
        'language' => 'en',
        'from' => '2026-06-11',
        'to' => '2026-07-19',
    ]);

    $matches = collect($response->json('Results', []))
        ->filter(fn (array $match) => Str::contains(strtolower($match['CompetitionName'][0]['Description'] ?? ''), 'world cup'))
        ->take(6)
        ->values()
        ->all();

    return response()->json($matches, $response->status());
});
""", styles['ReportCode']))
story.append(Spacer(1, 12))

story.append(Paragraph('3. Why this works', styles['ReportHeading2']))
story.append(Paragraph('The two fixes address the actual failure points:', styles['ReportBody']))
items2 = [
    'CORS is avoided because the browser only talks to the local Laravel backend, not the third-party FIFA endpoint directly.',
    'The data is now correct because the backend uses the official FIFA 2026 calendar feed instead of the older 2022 source.',
    'The filter guarantees the card only shows World Cup competition matches, which removes unrelated fixtures.',
]
story.append(ListFlowable([ListItem(Paragraph(x, styles['ReportBody'])) for x in items2], bulletType='bullet', leftIndent=18, bulletOffsetY=0))
story.append(Spacer(1, 12))

story.append(Paragraph('4. Verification evidence', styles['ReportHeading2']))
story.append(Paragraph('These checks were run after the fix:', styles['ReportBody']))
items3 = [
    'php artisan test --filter=LiveScoresRouteTest → 1 passed, 6 assertions',
    'npm run build → Vite production build completed successfully',
    'curl http://127.0.0.1:8000/api/live-scores → returned 3 FIFA World Cup 2026 matches, starting with Mexico vs South Africa',
]
story.append(ListFlowable([ListItem(Paragraph(x, styles['ReportBody'])) for x in items3], bulletType='bullet', leftIndent=18, bulletOffsetY=0))
story.append(Spacer(1, 12))

story.append(Paragraph('5. Result', styles['ReportHeading2']))
story.append(Paragraph('The live-score section now works with the correct FIFA World Cup 2026 match data. The issue was not only the network path; it was also the fact that the source itself was not the 2026 tournament feed.', styles['ReportBody']))
story.append(Spacer(1, 18))
story.append(Paragraph('Files involved: backend/routes/api.php (changed), frontend/wc_hub/src/components/LiveScores.vue (used by UI), backend/tests/Feature/LiveScoresRouteTest.php (created for verification).', styles['ReportCaption']))

doc = SimpleDocTemplate(pdf_path, pagesize=A4)
doc.build(story)
print('PDF created:', pdf_path)
