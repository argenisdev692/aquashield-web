@echo off
echo ========================================
echo INSTALANDO MCP SERVERS PARA CURSOR
echo ========================================
echo.

echo [1/7] Instalando FileSystem MCP...
npm install -g @modelcontextprotocol/server-filesystem

echo [2/7] Instalando Brave Search MCP...
npm install -g @modelcontextprotocol/server-brave-search

echo [3/7] Instalando GitHub MCP...
npm install -g @modelcontextprotocol/server-github

echo [4/7] Instalando Puppeteer MCP...
npm install -g @modelcontextprotocol/server-puppeteer

echo [5/7] Instalando Context7 MCP...
npm install -g @modelcontextprotocol/server-context7

echo [6/7] Instalando Sequential Thinking MCP...
npm install -g @modelcontextprotocol/server-sequential-thinking

echo [7/7] Instalando Memory MCP...
npm install -g @modelcontextprotocol/server-memory

echo.
echo ========================================
echo INSTALACION COMPLETADA!
echo ========================================
echo.
echo Ahora configura Cursor con el archivo cursor-mcp-config.json
echo.
pause 