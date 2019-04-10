import CGIHTTPServer
import SocketServer
import time
import threading

class NRWeb:
    loop = True

    def stopWeb(self):
        NRWeb.loop=False

    def webLoop(self):
        server_thread = threading.Thread(target=self.httpd.serve_forever)
        # Exit the server thread when the main thread terminates
        server_thread.daemon = True
        server_thread.start()
        # as using threads for clients, main thread only monitorizes end of loop, so just sleep and wait
        while NRWeb.loop == True:
            time.sleep(5) # check for end every 5 seconds,
        self.httpd.shutdown()
        self.httpd.server_close()

        # while NRWen.loop == True

    def __init__(self):
        handler=CGIHTTPServer.CGIHTTPRequestHandler
        self.httpd = SocketServer.TCPServer(("", 8000), handler)
        self.httpd.server_name = "localhost"
        self.httpd.server_port = 8000
