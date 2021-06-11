Feature: Asociar CFDI previamente cargado al ADD a póliza en contpaq on demand

  @ERPHI-302
  Scenario: prepóliza de factura de sao que ya generó una póliza en contpaq por medio del broker
    Dado que una prepóliza de sao ya genero una póliza en contpaq y no se asocio a su CFDI porque en el momento del lanzamieto a contpaq no estaba en el ADD
    Cuando se solicita realizar la asociación desde el sistema de contabilidad del ERP SAO
    Entonces se generará la asociación al CFDI y se verá reflejada en el apartado 'CFDI relacionados' de la ventana de consulta de la póliza
